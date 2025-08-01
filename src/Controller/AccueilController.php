<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articlesPublies = $entityManager->getRepository(Article::class)->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC']
        );

        return $this->render('accueil/index.html.twig', [
            'articles' => $articlesPublies,
        ]);
    }
}
