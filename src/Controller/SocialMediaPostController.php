<?php

namespace App\Controller;

use App\Entity\SocialMediaPost;
use App\Form\SocialMediaPostType;
use App\Repository\SocialMediaPostRepository;
use App\Service\FacebookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/socialmediapost')]
final class SocialMediaPostController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FacebookService $facebookService
    ) {}

    #[Route('/new', name: 'app_social_media_post_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $socialMediaPost = new SocialMediaPost();

        $form = $this->createForm(SocialMediaPostType::class, $socialMediaPost);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['error' => $form->getErrors(true)], 400);
        }

        $this->entityManager->persist($socialMediaPost);
        $this->entityManager->flush();

        $postPublishingStatus = $this->facebookService->publishPost($socialMediaPost);

        return $this->json([
            'message' => 'Post added successfully',
            'published' => $postPublishingStatus
        ]);
    }
}
