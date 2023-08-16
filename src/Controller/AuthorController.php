<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Author;

class AuthorController extends AbstractController
{

    #[Route('/author/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = new Author();

        $form = $this->createForm(GBType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $author->setName('Test');
            $author->setEmail('autoportal@email.ua');
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_g_b_edit', [
                        'uuid' => $author->getId(),
                            ], Response::HTTP_SEE_OTHER);
        }
    }

//    #[Route('/author', name: 'app_author')]
//    public function index(): Response
//    {
//        return $this->render('author/index.html.twig', [
//                    'controller_name' => 'AuthorController',
//        ]);
//    }
}
