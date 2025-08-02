<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $articlesPublies = $entityManager->getRepository(Article::class)->findBy(
            ['publie' => true],
            ['dateCreation' => 'DESC']
        );

        return $this->render('article/articles.html.twig', [
            'articles' => $articlesPublies,
        ]);
    }
}
