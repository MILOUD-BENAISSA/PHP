<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AdminAuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/admin/auteurs/nouveau', name: 'app_admin_author_create')]
    public function create(Request $request, AuthorRepository $repository): Response
    {
        // Création du formulaire
        $form = $this->createForm(AdminAuthorType::class);

        // On remplie le formulaire avec les données envoyé par l'utilisateur
        $form->handleRequest($request);

        // Tester si le formulaire est envoyé et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupération de l'auteur
            $author = $form->getData();

            // enregistrement des données en base de données
            $repository->add($author, true);

            // Redirection vers la page de la liste
            return $this->redirectToRoute('app_admin_author_list');
        }

        // Afficher la page
        return $this->render('admin/author/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/auteurs', name: 'app_admin_author_list')]
    public function list(AuthorRepository $repository): Response
    {
        // Récupérer les auteurs depuis la base de donnés
        $authors = $repository->findAll();

        // Afficher la page HTML
        return $this->render('admin/author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/admin/auteurs/{id}', name: 'app_admin_author_update')]
    public function update(int $id, Request $request, AuthorRepository $repository): Response
    {
        // récupérer l'auteur à partir de l'id
        $author = $repository->find($id);

        // Tester si le formulaire à était envoyé
        if ($request->isMethod('POST')) {
            // Récupérer les données rentré dans le formulaire
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $imageUrl = $request->request->get('imageUrl');

            // Mettre à jour les informations de l'auteur
            $author->setName($name);
            $author->setDescription($description);
            $author->setImageUrl($imageUrl);

            // Enregistrer l'auteur
            $repository->add($author, true);

            // rediriger vers la page liste
            return $this->redirectToRoute('app_admin_author_list');
        }

        // afficher le formulaire de mise à jour de l'auteur
        return $this->render('admin/author/update.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/admin/auteurs/{id}/supprimer', name: 'app_admin_author_remove')]
    public function remove(int $id, AuthorRepository $repository): Response
    {
        // Récupération de l'auteur depuis son id
        $author = $repository->find($id);

        // Supprimer l'auteur de la base de données
        $repository->remove($author, true);

        // Rediriger vers la liste
        return $this->redirectToRoute('app_admin_author_list');
    }
}
