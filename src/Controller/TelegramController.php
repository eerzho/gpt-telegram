<?php

namespace App\Controller;

use App\Service\TelegramApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/telegram')]
class TelegramController extends AbstractController
{
    public function __construct(private readonly TelegramApiService $telegramApiService)
    {
    }

    #[Route('/handle', name: 'app_telegram_handle', methods: ['POST'])]
    public function handle(): JsonResponse
    {
        $this->telegramApiService->runBot();

        return $this->json(['message' => 'Success!']);
    }
}
