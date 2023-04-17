<?php

namespace App\Controller;

use App\Service\TelegramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/webhook', name: 'app_telegram_webhook', methods: ['GET', 'POST'])]
    public function webhook(): JsonResponse
    {
        $this->telegramService->setWebhook(
            'https://6901-2a02-8308-500f-1600-71e8-a493-f9de-43a0.ngrok-free.app/telegram/handle'
        );

        return $this->json(['message' => 'Success!']);
    }

    #[Route('/commands', name: 'app_telegram_commands', methods: ['GET', 'POST'])]
    public function commands(): JsonResponse
    {
        $res = $this->telegramService->setCommands();

        return $this->json(['message' => 'Success!']);
    }
}
