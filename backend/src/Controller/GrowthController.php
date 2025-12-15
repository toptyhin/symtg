<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class GrowthController extends AbstractController
{
    #[Route('/shops/{shopId}/growth/telegram', name: 'app_growth_telegram')]

    public function __invoke(): Response
    {
        // Здесь вы можете рендерить Twig-шаблон, который подгружает React-приложение
        // и передает в него shopId, например, через глобальную переменную JS или data-атрибут.
        return $this->render('base.html.twig', [
            'shopId' => "blabla",
        ]);
    }
}