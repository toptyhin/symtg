<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    #[Route('/pia/shops/{shopId}/telegram/connect', name: 'connect_telegram', methods: ['POST'])]

    public function telegram(int $shopId, Request $request): JsonResponse
    {
        // Здесь вы можете рендерить Twig-шаблон, который подгружает React-приложение
        // и передает в него shopId, например, через глобальную переменную JS или data-атрибут.
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Telegram connected successfully',
        ]);
    }
}