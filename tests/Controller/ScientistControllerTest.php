<?php

namespace App\Test\Controller;

use App\Entity\Scientist;
use App\Repository\ScientistRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScientistControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ScientistRepository $repository;
    private string $path = '/scientist/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Scientist::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Scientist index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'scientist[Nationality]' => 'Testing',
            'scientist[Pet]' => 'Testing',
            'scientist[Drink]' => 'Testing',
            'scientist[House]' => 'Testing',
            'scientist[Cigarettes]' => 'Testing',
        ]);

        self::assertResponseRedirects('/scientist/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Scientist();
        $fixture->setNationality('My Title');
        $fixture->setPet('My Title');
        $fixture->setDrink('My Title');
        $fixture->setHouse('My Title');
        $fixture->setCigarettes('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Scientist');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Scientist();
        $fixture->setNationality('My Title');
        $fixture->setPet('My Title');
        $fixture->setDrink('My Title');
        $fixture->setHouse('My Title');
        $fixture->setCigarettes('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'scientist[Nationality]' => 'Something New',
            'scientist[Pet]' => 'Something New',
            'scientist[Drink]' => 'Something New',
            'scientist[House]' => 'Something New',
            'scientist[Cigarettes]' => 'Something New',
        ]);

        self::assertResponseRedirects('/scientist/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNationality());
        self::assertSame('Something New', $fixture[0]->getPet());
        self::assertSame('Something New', $fixture[0]->getDrink());
        self::assertSame('Something New', $fixture[0]->getHouse());
        self::assertSame('Something New', $fixture[0]->getCigarettes());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Scientist();
        $fixture->setNationality('My Title');
        $fixture->setPet('My Title');
        $fixture->setDrink('My Title');
        $fixture->setHouse('My Title');
        $fixture->setCigarettes('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/scientist/');
    }
}
