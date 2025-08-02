<?php

namespace App\Controller;

use App\Form\ArticleSearchType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleSearchType::class);
        $form->handleRequest($request);

        $articles = null;

        // ✅ On vérifie si le champ "query" est défini dans les paramètres GET
        $queryParams = $request->query->all('article_search');
        $query = $queryParams['query'] ?? null;


        if ($query !== null && $query !== '') {
            $articles = $articleRepository->findBySearch($query);
        }

        return $this->render('article/articles.html.twig', [
            'articles' => $articles,
            'searchForm' => $form->createView(),
        ]);
    }
}
