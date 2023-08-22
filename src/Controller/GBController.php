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
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;

#[Route('/gb')]
class GBController extends AbstractController
{

    #[Route('/', name: 'app_g_b_index', methods: ['GET'])]
    public function index(GBRepository $gBRepository, UserInterface $user): Response
    {
        return $this->render('gb/index.html.twig', [
                    'g_bs' => $gBRepository->findBy(['user_id' => $user->getId()], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_g_b_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        $gB = new GB();

        $form = $this->createForm(GBType::class, $gB);
//        $form->add('captcha', CaptchaType::class, [
//            'label_attr' => [
//                'class' => 'form-group mt-4 mb-4',
//                'for' => "captcha",
//        ]]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been created successfuly.');
            
            $uuid = Uuid::v4();            
            $gB->setUuid($uuid);            
            $gB->setUser($user);
            $gB->setApproved(1);

            $entityManager->persist($gB);
            $entityManager->flush();

            return $this->redirectToRoute('app_g_b_edit', [
                        'uuid' => $uuid
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/new.html.twig', [
                    'g_b' => $gB,
                    'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_g_b_show', methods: ['GET'])]
    public function show(GB $gB): Response
    {
        return $this->render('gb/show.html.twig', [
                    'gb' => $gB,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_g_b_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GB $gB, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GBType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been updated successfuly.');
            $entityManager->flush();

            return $this->redirectToRoute('app_g_b_edit', [
                        'action' => 'edit',
                        'uuid' => $gB->getUuid()
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/edit.html.twig', [
                    'g_b' => $gB,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_g_b_delete', methods: ['POST'])]
    public function delete(Request $request, GB $gB, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gB->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gB);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_g_b_index', [], Response::HTTP_SEE_OTHER);
    }
}
