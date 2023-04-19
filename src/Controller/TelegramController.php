<?php

namespace App\Controller;

use App\Service\TelegramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/telegram')]
class TelegramController extends AbstractController
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    #[Route('/handle', name: 'app_telegram_handle', methods: ['GET', 'POST'])]
    public function handle(): JsonResponse
    {
        $this->telegramService->answerByWebhook();

        return $this->json(['message' => 'Success!']);
    }
}
