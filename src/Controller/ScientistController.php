<?php

namespace App\Controller;

use App\Entity\Scientist;
use App\Form\ScientistType;
use App\Helper\ListHelper;
use App\Repository\ScientistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/scientist')]
class ScientistController extends AbstractController
{
    #[Route('/', name: 'app_scientist_index', methods: ['GET'])]
    public function index(ScientistRepository $scientistRepository): Response
    {
        return $this->render('scientist/index.html.twig', [
            'scientists' => $scientistRepository->findAll(),
        ]);
    }

    #[Route('/composite-list', name: 'app_scientist_composite_list', methods: ['GET'])]
    public function compositeList(ScientistRepository $scientistRepository): Response
    {
        return $this->render('scientist/composite-list.html.twig', [
            'scientists' => $scientistRepository->findAll(),
        ]);
    }

    #[Route('/composite-list-data', name: 'app_scientist_composite_list_data', methods: ['GET'])]
    public function compositeListData(Request $request, LoggerInterface $logger, EntityManagerInterface $em): Response
    {
        $parameters = $request->query->all();
        $draw = $parameters['draw'];
        $start = $parameters['start'];
        $length = $parameters['length'];
        $columnsParam = $parameters['columns'];
        $logger->debug("columnsParam: " . json_encode($columnsParam));
//        $columns = array_column($columnsParam, 'name');
        $columns = ["houseNumber", "houseColor", "nationality", "cigarettesBrand", "drinkType", "petType"];
        $orderParam = $parameters['order'];
        $search = $parameters['search'];

        $columnsMap = [
            "id" => "sc.id",
            "houseNumber" => "ho.Number",
            "houseColor" => "ho.Color",
            "nationality" => "sc.Nationality",
            "cigarettesBrand" => "ci.Brand",
            "drinkType" => "dr.Type",
            "petType" => "pet.Type"
        ];
        $listHelper = new ListHelper($logger, 'sc', $columnsMap);
        $qb = $listHelper->queryBuilder(
            $em,
            $columns,
            []
        );
        $logger->debug("query dataTable:" . $qb->getQuery()->getSQL());
        $rows = $qb->getQuery()->setFirstResult($start)->setMaxResults($length)->execute();
        $rowsDataTable = array_map('array_values', $rows);
        $logger->debug("rowsDataTable:" . json_encode($rowsDataTable));
        $recordsTotal = $listHelper->queryBuilder($em, [], [])->getQuery()->getSingleResult()["1"];
//        $logger->debug("filtered query count" . $listHelper->queryBuilder($em, [], $filters)->getDQL());
        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $rowsDataTable
        ]);
    }

    #[Route('/new', name: 'app_scientist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ScientistRepository $scientistRepository): Response
    {
        $scientist = new Scientist();
        $form = $this->createForm(ScientistType::class, $scientist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scientistRepository->add($scientist, true);

            return $this->redirectToRoute('app_scientist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('scientist/new.html.twig', [
            'scientist' => $scientist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_scientist_show', methods: ['GET'])]
    public function show(Scientist $scientist): Response
    {
        return $this->render('scientist/show.html.twig', [
            'scientist' => $scientist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_scientist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scientist $scientist, ScientistRepository $scientistRepository): Response
    {
        $form = $this->createForm(ScientistType::class, $scientist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scientistRepository->add($scientist, true);

            return $this->redirectToRoute('app_scientist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('scientist/edit.html.twig', [
            'scientist' => $scientist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_scientist_delete', methods: ['POST'])]
    public function delete(Request $request, Scientist $scientist, ScientistRepository $scientistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scientist->getId(), $request->request->get('_token'))) {
            $scientistRepository->remove($scientist, true);
        }

        return $this->redirectToRoute('app_scientist_index', [], Response::HTTP_SEE_OTHER);
    }
}
