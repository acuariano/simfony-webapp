<?php


namespace App\Helper;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;

/**
 * Makes easier the creation of queries for lists with paging and filtering.
 * Allows to join different tables/objects in the same list.
 * The objectAlias parameter specifies the principal query object (e.g. Scientist). The rest are joined based on
 * filter and select columns.
 * The columns are defined in a map. The map keys are based on client list columns (e.g. used in jQuery DataTable)
 * The values are references to fields in entities using aliasMap for the joining objects.
 * All supported objects are defined in aliasMap. The map using the alias for each object as keys and contains:
 *  - objectName: the full entity name for the referenced object associated to the alias.
 *  - joinMap: a map with the join relationship from the current object (e.g. sc) to the target object (e.g. Pet)
 * Filters accumulate into an array using the methods addFilter, addArrayFilter and operatorFilter.
 * Once initialized, use method queryBuilder to create a QueryBuilder object passing in the the selected columns
 * and filters. Optionally add a orderBy parameter.
 *
 * Class ListHelper
 * @package App\Helper
 */
class ListHelper
{
    private static $aliasMap = [
            'sc' => [
                'objectName' => 'App\\Entity\\Scientist',
                'joinMap' => [
                    'pet' => 'sc.Pet',
                    'dr' => 'sc.Drink',
                    'ho' => 'sc.House',
                    'ci' => 'sc.Cigarettes',
                ]
            ],
            'pet' => [
                'objectName' => 'Pet',
                'joinMap' => [
                    'sc' => 'pet.Scientist'
                ]
            ],
            'dr' => [
                'objectName' => 'Drink',
                'joinMap' => [
                    'sc' => 'pet.Scientist'
                ]
            ],
            'ho' => [
                'objectName' => 'House',
                'joinMap' => [
                    'sc' => 'pet.Scientist'
                ]
            ],
            'ci' => [
                'objectName' => 'Cigarettes',
                'joinMap' => [
                    'sc' => 'pet.Scientist'
                ]
            ]
        ];

    /** @var string */
    private $objectAlias;

    /**
     * Map<String, String>
     * @var array
     */
    private $columnsMap;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct($logger, $objectAlias, $columnsMap)
    {
        $this->logger = $logger;
        $this->objectAlias = $objectAlias;
        $this->columnsMap = $columnsMap;
    }

    public static function getAliasForObjectName($objectName)
    {
        foreach (self::$aliasMap as $alias => $array) {
            if ($objectName == $array['objectName']) {
                return $alias;
            }
        }
        return null;
    }

    /**
     * Parses a filter in the form "<op><value>" into "<field> <op> <value>", where <op> is one of <,<=,=,>=,>
     * Example: $filter="<=1000", $field="d.monto" => "d.monto <= 1000"
     * Value has to be either number or string. String will be escaped and quotes added.
     * @return string
     */
    public static function operatorFilter($filter, $field)
    {
        $found = preg_match('/(<=|>=|<|=|>)(.*)/', $filter, $matches);
        if ($found) {
            $operator = $matches[1];
            $value = $matches[2];
            if (!is_numeric($value)) {
                $value = "'" . str_replace("'", "''") . "'";
            }
            return "$field $operator $value";
        }
        throw new ListException("Invalid operator filter: $filter");
    }

    /**
     * @param EntityManagerInterface $em
     * @param $columns
     * @param array $filters
     * @param null $orderBy
     * @return QueryBuilder
     * @throws ListException
     */
    public function queryBuilder(EntityManagerInterface $em, $columns, $filters, $orderBy = null) {
        $selectColumns = [];
        $joinAliases = [];
        foreach ($columns as $column) {
            if (!array_key_exists($column, $this->columnsMap)) {
                throw new ListException("Column not defined '$column'");
            }
            $selectColumn = $this->columnsMap[$column];
            $selectColumns[] = "$selectColumn $column";
            $alias = $this->extractAlias($selectColumn);
            if ($alias != null) {
                $joinAliases[] = $alias;
            }
        }
        foreach ($filters as $filter) {
            foreach($this->columnsMap as $value) {
                if (strpos($filter, $value) !== false) {
                    $alias = $this->extractAlias($value);
                    if ($alias != null) {
                        $joinAliases[] = $alias;
                    }
                }
            }
        }


//        $this->logger->debug("objectAlias={$this->objectAlias}");
        $objectName = self::$aliasMap[$this->objectAlias]['objectName'];
        if (empty($columns)) {
            $queryBuilder = $em->createQueryBuilder()->select("count({$this->objectAlias}.id)")
                ->from($objectName, $this->objectAlias);
        } else {
            $queryBuilder = $em->createQueryBuilder()->select(implode(', ', $selectColumns))
                ->from($objectName, $this->objectAlias);
        }
//        $this->logger->debug("joinAliases=" . json_encode($joinAliases));
        $joinAliases = array_unique($joinAliases);
//        $this->logger->debug("joinAliases after unique=" . json_encode($joinAliases));
        $availableAliases = self::$aliasMap[$this->objectAlias]['joinMap'];
        foreach($joinAliases as $alias) {
            if (array_key_exists($alias, $availableAliases) && $alias != $this->objectAlias) {
//                $this->logger->debug("adding join " . $availableAliases[$alias] . " with alias $alias");
                $queryBuilder->leftJoin($availableAliases[$alias], $alias, Join::WITH);
                if (array_key_exists($alias, self::$aliasMap)) {
//                    $this->logger->debug("adding availableAliases for $alias: " . json_encode(self::$aliasMap[$alias]));
                    $availableAliases = array_merge($availableAliases, self::$aliasMap[$alias]['joinMap']);
                }
            }
        }
        foreach ($filters as $filter) {
            $queryBuilder->andWhere($filter);
        }
        if (!empty($orderBy)) {
            $orderSplit = explode(" ", $orderBy);
            $field = $orderSplit[0];
            if (count($orderSplit) == 2) {
                $order = $orderSplit[1];
            } else {
                $order = "ASC";
            }
            $queryBuilder->orderBy($field, $order);
        }
        return $queryBuilder;
    }

    public function formatDates($rows) {
        $rowsFormatted = [];
        foreach($rows as $row) {
            $newRow = [];
            foreach ($row as $key => $value) {
                if ($value instanceof DateTime) {
                    $newRow[$key] = $value->format('Y-m-d\TH:i:s.000\Z');
                } else {
                    $newRow[$key] = $value;
                }
            }
            $rowsFormatted[] = $newRow;
        }
        return $rowsFormatted;
    }

    /**
     * @throws ListException
     */
    public static function addFilter(&$filters, $filterStr, $value) {
        if ($value === 0 || !empty($value)) {
            if (strpos($value, "'") !== false) {
                throw new ListException("Quotes are not allowed in filter: \"$value\"");
            }
            $filters[] = str_replace("<value>", $value, $filterStr);
        }

    }

    public static function addArrayFilter(&$filters, $filterStr, $value) {
        if (!empty($value)) {
            foreach ($value as $v) {
                if (strpos($v, "'") !== false) {
                    throw new ListException("Quotes are not allowed in filter: \"$v\"");
                }
            }

            if (is_numeric($value[0])) {
                $arrayValues = implode(',', $value);
            } else {
                $arrayValues = self::arrayStringValue($value);
            }
            $filters[] = str_replace("<value>", "($arrayValues)", $filterStr);
        }

    }

    /**
     * @param $column string ejemplos: sc.Nationality, ci.Brand
     * @return string
     */
    private function extractAlias($column)
    {
        $posDot = strpos($column, '.');
        $alias = null;
        if ($posDot !== false) {
            $len = $posDot;
            $offset = 0;
            $posIdentity = strpos($column, 'IDENTITY(');
            if ($posIdentity === 0) {
                $offset = strlen('IDENTITY(');
                $len = $len - $offset;
            }
            $alias = substr($column, $offset, $len);
//                $this->logger->debug("selectColumn: $selectColumn, posidentity=$posIdentity, offset=$offset, len=$len, alias=$alias");
        }
        return $alias;
    }

    private static function arrayStringValue($values)
    {
        return implode(',', array_map(function($value) { return "'$value'";}, $values));
    }
}
