<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Article;
use App\Entity\User;
use App\Form\CategorieType;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function admin(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé à l’administrateur.');
        }

        // ----- Formulaire de catégorie -----
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);

        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('app_admin');
        }

        // ----- Formulaire d’article -----
        $article = new Article();
        $article->setAuteur($this->getUser());
        $article->setDateCreation(new \DateTime());

        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);

        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $imageFile = $formArticle->get('image')->getData();

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

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_admin');
        }

        // ----- Liste des utilisateurs -----
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'formCategorie' => $formCategorie->createView(),
            'formArticle' => $formArticle->createView(),
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/supprimer', name: 'admin_user_supprimer')]
    public function supprimerUser(int $id, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Impossible de supprimer un administrateur.');
            return $this->redirectToRoute('app_admin');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('app_admin');
    }
}
