<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GBRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\GB;
use App\Form\GBType;

#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin_index')]
    public function index(GBRepository $gBRepository): Response
    {
        return $this->render('admin/gb/index.html.twig', [
                    'g_bs' => $gBRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/{uuid}', name: 'app_admin_entry_show', methods: ['GET'])]
    public function show(GB $gB): Response
    {
        return $this->render('admin/gb/show.html.twig', [
                    'gb' => $gB,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_admin_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GB $gB, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GBType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been updated successfuly.');
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_entry_edit', [                        
                        'uuid' => $gB->getUuid()
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/gb/edit.html.twig', [
                    'g_b' => $gB,
                    'form' => $form,
        ]);
    }

    #[Route('/{uuid}/trash', name: 'app_admin_entry_delete', methods: ['POST'])]
    public function delete(Request $request, GB $gB, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $token = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($gB);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('entry.deleted_successfully'));

        return $this->redirectToRoute('app_admin');
    }
}
