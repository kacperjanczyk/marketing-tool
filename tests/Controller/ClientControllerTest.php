<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ClientControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/client/controller/new/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Client::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Client index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'client[ceidgId]' => 'Testing',
            'client[name]' => 'Testing',
            'client[address]' => 'Testing',
            'client[city]' => 'Testing',
            'client[region]' => 'Testing',
            'client[country]' => 'Testing',
            'client[postCode]' => 'Testing',
            'client[ownerName]' => 'Testing',
            'client[ownerSurname]' => 'Testing',
            'client[phone]' => 'Testing',
            'client[email]' => 'Testing',
            'client[www]' => 'Testing',
            'client[ceidgUrl]' => 'Testing',
            'client[taxId]' => 'Testing',
            'client[status]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Client();
        $fixture->setCeidgId('My Title');
        $fixture->setName('My Title');
        $fixture->setAddress('My Title');
        $fixture->setCity('My Title');
        $fixture->setRegion('My Title');
        $fixture->setCountry('My Title');
        $fixture->setPostCode('My Title');
        $fixture->setOwnerName('My Title');
        $fixture->setOwnerSurname('My Title');
        $fixture->setPhone('My Title');
        $fixture->setEmail('My Title');
        $fixture->setWww('My Title');
        $fixture->setCeidgUrl('My Title');
        $fixture->setTaxId('My Title');
        $fixture->setStatus('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Client');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Client();
        $fixture->setCeidgId('Value');
        $fixture->setName('Value');
        $fixture->setAddress('Value');
        $fixture->setCity('Value');
        $fixture->setRegion('Value');
        $fixture->setCountry('Value');
        $fixture->setPostCode('Value');
        $fixture->setOwnerName('Value');
        $fixture->setOwnerSurname('Value');
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setWww('Value');
        $fixture->setCeidgUrl('Value');
        $fixture->setTaxId('Value');
        $fixture->setStatus('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'client[ceidgId]' => 'Something New',
            'client[name]' => 'Something New',
            'client[address]' => 'Something New',
            'client[city]' => 'Something New',
            'client[region]' => 'Something New',
            'client[country]' => 'Something New',
            'client[postCode]' => 'Something New',
            'client[ownerName]' => 'Something New',
            'client[ownerSurname]' => 'Something New',
            'client[phone]' => 'Something New',
            'client[email]' => 'Something New',
            'client[www]' => 'Something New',
            'client[ceidgUrl]' => 'Something New',
            'client[taxId]' => 'Something New',
            'client[status]' => 'Something New',
        ]);

        self::assertResponseRedirects('/client/controller/new/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCeidgId());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getCity());
        self::assertSame('Something New', $fixture[0]->getRegion());
        self::assertSame('Something New', $fixture[0]->getCountry());
        self::assertSame('Something New', $fixture[0]->getPostCode());
        self::assertSame('Something New', $fixture[0]->getOwnerName());
        self::assertSame('Something New', $fixture[0]->getOwnerSurname());
        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getWww());
        self::assertSame('Something New', $fixture[0]->getCeidgUrl());
        self::assertSame('Something New', $fixture[0]->getTaxId());
        self::assertSame('Something New', $fixture[0]->getStatus());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Client();
        $fixture->setCeidgId('Value');
        $fixture->setName('Value');
        $fixture->setAddress('Value');
        $fixture->setCity('Value');
        $fixture->setRegion('Value');
        $fixture->setCountry('Value');
        $fixture->setPostCode('Value');
        $fixture->setOwnerName('Value');
        $fixture->setOwnerSurname('Value');
        $fixture->setPhone('Value');
        $fixture->setEmail('Value');
        $fixture->setWww('Value');
        $fixture->setCeidgUrl('Value');
        $fixture->setTaxId('Value');
        $fixture->setStatus('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/client/controller/new/');
        self::assertSame(0, $this->repository->count([]));
    }
}
