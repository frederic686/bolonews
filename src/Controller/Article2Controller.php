<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Article2Controller extends AbstractController
{
    #[Route('/article2', name: 'app_article2')]
    public function index(): Response
    {
        return $this->render('article2/index.html.twig', [
            'controller_name' => 'Article2Controller',
        ]);
    }
}
