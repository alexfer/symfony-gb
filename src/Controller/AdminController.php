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

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(GBRepository $gBRepository): Response
    {        
        return $this->render('admin/index.html.twig', [
            'g_bs' => $gBRepository->findBy([], ['id' => 'DESC']),
        ]);
    }
    
    #[Route('/{uuid}', name: 'app_admin_entry_show', methods: ['GET'])]
    public function show(GB $gB): Response
    {
        return $this->render('admin/show.html.twig', [
                    'gb' => $gB,
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
