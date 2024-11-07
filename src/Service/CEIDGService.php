<?php

namespace App\Service;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CEIDGService
{
    private const API_URL = 'https://dane.biznes.gov.pl/api/ceidg/v2/';
    private string $jwtToken;
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly ClientRepository $clientRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        ParameterBagInterface $params
    ) {
        $this->jwtToken = $params->get('CEIDG_API_JWT_TOKEN');
    }

    public function fetchAndSaveClientsFiltered(array $filters = []): string|array
    {
        $clients = $this->fetchClientsFiltered($filters);

        if (!is_array($clients)) {
            return $clients;
        }

        $clients = $this->fetchExtendedClientDataAndNormalize($clients);

        return $this->saveClients($clients);
    }

    private function fetchClientsFiltered(array $filters = []): string|array
    {
        $queryParams = [];

        if (isset($filters['city']) && $filters['city'] && $filters['city'] !== '') {
            $queryParams['miasto'] = $filters['city'];
        }
        if (isset($filters['region']) && $filters['region'] && $filters['region'] !== '') {
            $queryParams['wojewodztwo'] = $filters['region'];
        }
        if (isset($filters['district']) && $filters['district'] && $filters['district'] !== '') {
            $queryParams['powiat'] = $filters['district'];
        }
        if (isset($filters['postCode']) && $filters['postCode'] && $filters['postCode'] !== '') {
            $queryParams['kod'] = $filters['postCode'];
        }
        if (isset($filters['street']) && $filters['street'] && $filters['street'] !== '') {
            $queryParams['ulica'] = $filters['street'];
        }
        $queryParams['status'] = 'AKTYWNY';

        $clients = [];

        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->jwtToken,
                'Accept' => 'application/json',
            ];

            do {
                $request = $this->httpClient->request('GET', $response['links']['next'] ?? self::API_URL . 'firmy', [
                    'query' => $queryParams,
                    'headers' => $headers
                ]);

                $response = $request->toArray();
                foreach ($response['firmy'] as $clientData) {
                    $clients[] = $clientData;
                }
            }
            while ($request->getInfo('url') !== $response['links']['next']);

        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return 'Failed to fetch data: ' . $e->getMessage();
        }


        return $clients;
    }

    private function fetchExtendedClientDataAndNormalize(array $rawData): array
    {
        $normalizedClients = [];
        foreach ($rawData as $rawClientData) {
            $extendedClientData = $this->fetchExtendedClientData($rawClientData['id']);
            $normalizedClients[] = $this->normalizeClientDataAndCreateEntity($extendedClientData);
        }

        return $normalizedClients;
    }

    private function fetchExtendedClientData(string $id): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->jwtToken,
            'Accept' => 'application/json',
        ];

        try {
            $response = $this->httpClient->request('GET', self::API_URL . 'firma/' . $id, [
                'headers' => $headers,
            ]);
            return $response->toArray();
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->warning($e->getMessage());
        }

        return [];
    }

    private function normalizeClientDataAndCreateEntity(array $clientData): ?array
    {
        if (isset($clientData['firma'][0]) && is_array($clientData['firma'][0]) && $clientData['firma'][0] !== []) {
            $clientData = $clientData['firma'][0];
            return [
                'ceidgId' => $clientData['id'] ?? '',
                'name' => $clientData['nazwa'] ?? '',
                'address' => $this->buildClientAddressField($clientData['adresDzialalnosci']),
                'city' => $clientData['adresDzialalnosci']['miasto'] ?? '',
                'region' => $clientData['adresDzialalnosci']['wojewodztwo'] ?? '',
                'country' => $clientData['adresDzialalnosci']['kraj'] ?? '',
                'postCode' => $clientData['adresDzialalnosci']['kod'] ?? '',
                'ownerName' => $clientData['wlasciciel']['imie'] ?? '',
                'ownerSurname' => $clientData['wlasciciel']['nazwisko'] ?? '',
                'phone' => $clientData['telefon'] ?? '',
                'email' => $clientData['email'] ?? '',
                'www' => $clientData['www'] ?? '',
                'ceidgUrl' => $clientData['link'] ?? '',
                'taxId' => $clientData['wlasciciel']['nip'] ?? '',
                'status' => 'lead',
            ];
        }

        return null;
    }

    private function buildClientAddressField(array $addressData): string
    {
        $addressField = $addressData['ulica'] . ' ' . $addressData['budynek'];
        if (isset($addressData['lokal']) && $addressData['lokal'] && $addressData['lokal'] !== '') {
            $addressField .= '/' . $addressData['lokal'];
        }
        return $addressField;
    }

    private function saveClients(array $clients): array
    {
        $result = [
            'added' => 0,
            'skipped' => 0,
            'error' => 0
        ];
        foreach ($clients as $clientData) {
            if ($clientData === null) {
                continue;
            }

            $client = $this->clientRepository->findBy(['ceidgId' => $clientData['ceidgId']]);
            if ($client) {
                $result['skipped']++;
                continue;
            }

            $client = $this->clientRepository->findBy(['taxId' => $clientData['taxId']]);
            if ($client) {
                $result['skipped']++;
                continue;
            }

            $client = new Client();
            $form = $this->formFactory->create(ClientType::class, $client);
            $form->submit($clientData);

            if (!$form->isValid()) {
                $result['error']++;
                $this->logger->warning('Failed to create client: ' . $form->getErrors(true));
                continue;
            }

            $this->entityManager->persist($client);
            $result['added']++;
        }
        $this->entityManager->flush();

        return $result;
    }
}
