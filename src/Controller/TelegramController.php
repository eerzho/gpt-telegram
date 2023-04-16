<?php

namespace App\Controller;

use App\Service\TelegramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/telegram')]
class TelegramController extends AbstractController
{
    public function __construct(
        private readonly TelegramService $telegramService
    ) {
    }

    #[Route('/handle', name: 'app_telegram_handle', methods: ['GET', 'POST'])]
    public function handle(): JsonResponse
    {
        $this->telegramService->answerByWebhook();

        return $this->json(['message' => 'Success!']);
    }

    #[Route('/updates', name: 'app_telegram_updates', methods: ['GET', 'POST'])]
    public function updates(ParameterBagInterface $parameterBag): JsonResponse
    {
        $this->telegramService->answerByUpdates();

        return $this->json(['message' => 'Success!']);
    }

    #[Route('/webhook', name: 'app_telegram_webhook', methods: ['GET', 'POST'])]
    public function webhook(): JsonResponse
    {
        $this->telegramService->setWebhook(
            'https://44ac-2a02-8308-500f-1600-dcea-6177-a260-397f.ngrok-free.app/telegram/handle'
        );

        return $this->json(['message' => 'Success!']);
    }
}
