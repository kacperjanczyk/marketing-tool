<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Service\CEIDGService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
final class ClientController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CEIDGService $CEIDGService,
    )
    {}

    #[Route('/new', name: 'app_client_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $client = new Client();

        $form = $this->createForm(ClientType::class, $client);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)], 400);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->json(['message' => 'Client added successfully']);
    }

    #[Route('/import', name: 'app_client_import', methods: ['GET'])]
    public function import(Request $request): Response
    {
        $filters = [
            'city' => $request->query->get('city'),
            'region' => $request->query->get('region'),
            'district' => $request->query->get('district'),
            'postCode' => $request->query->get('postCode'),
            'street' => $request->query->get('street'),
        ];

        $filters = array_filter($filters);
        $clients = $this->CEIDGService->fetchClientsFiltered($filters);

        if (is_array($clients)) {
            return $this->json([
                'data' => $clients
            ]);
        }

        return $this->json([
            'error' => $clients
        ], 500);
    }
}
