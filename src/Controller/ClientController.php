<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
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
        private readonly CEIDGService $CEIDGService,
    )
    {}

    #[Route(name: 'app_client_controller_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client_controller/index.html.twig', [
            'clients' => $clientRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_controller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client_controller/new.html.twig', [
            'client' => $client,
            'form' => $form
        ]);
    }

    #[Route('/{id}/show', name: 'app_client_controller_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client_controller/edit.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_controller_details', methods: ['PUT'])]
    public function details(Request $request, EntityManagerInterface $entityManager, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_controller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client_controller/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_client_controller_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_controller_index', [], Response::HTTP_SEE_OTHER);
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
        $clients = $this->CEIDGService->fetchAndSaveClientsFiltered($filters);

        if (!is_array($clients)) {
            return $this->json([
                'error' => $clients
            ], 500);
        }

        return $this->json([
            'message' => 'Clients import finished',
            'data' => $clients
        ]);
    }
}
