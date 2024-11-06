<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
        ParameterBagInterface $params
    ) {
        $this->jwtToken = $params->get('CEIDG_API_JWT_TOKEN');
    }

    public function fetchClientsFiltered(array $filters = []): string|array
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
}
