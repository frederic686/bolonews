<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ArticleRepository $articleRepository,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // ----- Edition ou Création d’un article -----
        $editId = $request->query->get('edit');
        $article = $editId
            ? $entityManager->getRepository(Article::class)->findOneBy(['id' => $editId, 'auteur' => $user])
            : new Article();

        if ($editId && !$article) {
            throw $this->createNotFoundException('Article non trouvé ou non autorisé.');
        }

        if (!$editId) {
            $article->setAuteur($user);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $article->setImage($newFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        // ----- Suppression -----
        if ($request->query->get('delete')) {
            $deleteId = (int) $request->query->get('delete');
            $toDelete = $entityManager->getRepository(Article::class)->findOneBy(['id' => $deleteId, 'auteur' => $user]);
            if ($toDelete) {
                $entityManager->remove($toDelete);
                $entityManager->flush();
                return $this->redirectToRoute('app_user');
            }
        }

        // ----- Liste des articles de l’utilisateur -----
        $articles = $articleRepository->findBy([
            'auteur' => $user,
            'publie' => true,
        ], ['dateCreation' => 'DESC']);

        $articlesNonPublies = $articleRepository->findBy([
            'auteur' => $user,
            'publie' => false,
        ], ['dateCreation' => 'DESC']);

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'articlesNonPublies' => $articlesNonPublies,
            'editId' => $editId,
        ]);
    }
}
