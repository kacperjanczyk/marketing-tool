<?php

namespace App\Service;

use App\Entity\SocialMediaPost;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookService
{
    private const API_URL = 'https://graph.facebook.com/v21.0/me/feed';
    private string $accessToken;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        ParameterBagInterface $params
    )
    {
        $this->accessToken = $params->get('FACEBOOK_ACCESS_TOKEN');
    }

    public function publishPost(SocialMediaPost $post): bool
    {
        $queryParams = [
            'message' => $post->getText()
        ];

        if ($post->getPublishDate()) {
            $queryParams['published'] = false;
            $queryParams['scheduled_publish_time'] = (string)$post->getPublishDate();
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
        ];

        try {
            $request = $this->httpClient->request('POST', self::API_URL, [
                'query' => $queryParams,
                'headers' => $headers
            ]);
            $response = $request->toArray();
            if (!isset($response['id'])) {
                throw new RuntimeException(json_encode($response));
            }

            $post->setStatus('published');
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        } catch (TransportExceptionInterface|ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|RuntimeException $e) {
            $post->setStatus('publish_error');
            $this->logger->error('Publishing social media post (' . $post->getId() . ') error: ' . $e->getMessage());
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            return false;
        }

        return true;
    }
}
