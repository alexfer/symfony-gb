<?php

namespace App\Controller;

use App\Entity\GB;
use App\Form\GBType;
use App\Repository\GBRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class GBController extends AbstractController
{
    #[Route('/', name: 'app_g_b_index', methods: ['GET'])]
    public function index(GBRepository $gBRepository): Response
    {
        return $this->render('gb/index.html.twig', [
            'g_bs' => $gBRepository->findBy(array(), array('id' => 'DESC')),
        ]);
    }

    #[Route('/new', name: 'app_g_b_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gB = new GB();
        $form = $this->createForm(GBType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gB);
            $entityManager->flush();

            return $this->redirectToRoute('app_g_b_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/new.html.twig', [
            'g_b' => $gB,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_g_b_show', methods: ['GET'])]
    public function show(GB $gB): Response    
    {        
        return $this->render('gb/show.html.twig', [
            'gb' => $gB,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_g_b_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GB $gB, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GBType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();            

            return $this->redirectToRoute('app_g_b_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/edit.html.twig', [
            'g_b' => $gB,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_g_b_delete', methods: ['POST'])]
    public function delete(Request $request, GB $gB, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gB->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gB);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_g_b_index', [], Response::HTTP_SEE_OTHER);
    }
}
