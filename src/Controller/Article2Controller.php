<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Article2Controller extends AbstractController
{
    #[Route('/article2/{id}', name: 'app_article2')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article || !$article->isPublie()) {
            throw $this->createNotFoundException('Article introuvable ou non publié.');
        }

        return $this->render('article2/index.html.twig', [
            'article' => $article,
        ]);
    }

}
