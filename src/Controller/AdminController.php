<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GBRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\{
    GB,
    Attach,
};
use App\Form\{    
    AttachType,
};

#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin_index')]
    public function index(GBRepository $gbRepository): Response
    {
        return $this->render('admin/gb/index.html.twig', [
                    'gbs' => $gbRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/{uuid}', name: 'app_admin_entry_show', methods: ['GET'])]
    public function show(GB $gb): Response
    {
        return $this->render('admin/gb/show.html.twig', [
                    'gb' => $gb,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_admin_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GB $gb, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AttachType::class, $gb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been updated successfuly.');
            $entityManager->flush();
            
            $source = $form->get('name')->getData();

            if ($source) {
                $originalFilename = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = sprintf("%s-%s.%s", $safeFilename, uniqid(), $source->guessExtension());

                try {
                    $source->move(
                            $info = $this->getParameter('kernel.project_dir') . '/public/attachments/entry/' . $gb->getId(),
                            $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }

                $filePath = $info . '/' . $newFilename;

                $attach = new Attach();
                $attach->setGB($gb);
                $attach->setName($newFilename)
                        ->setGB($gb)
                        ->setSize(filesize($filePath))
                        ->setMime(mime_content_type($filePath));

                $entityManager->persist($attach);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_admin_entry_edit', [
                        'uuid' => $gb->getUuid()
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/gb/edit.html.twig', [
                    'gb' => $gb,
                    'form' => $form,
        ]);
    }

    #[Route('/{uuid}/trash', name: 'app_admin_entry_delete', methods: ['POST'])]
    public function delete(Request $request, GB $gb, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $token = $request->request->get('token');

        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($gb);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('entry.deleted_successfully'));

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/{uuid}/approve', name: 'app_admin_entry_approve', methods: ['GET'])]
    public function approve(Request $request, GB $gb, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $gb->setApproved(1);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('message.approved_entry_successfully'));
        return $this->redirectToRoute('app_admin_entry_show', [
                    'uuid' => $gb->getUuid()
                        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{uuid}/reject', name: 'app_admin_entry_reject', methods: ['GET'])]
    public function reject(Request $request, GB $gb, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $gb->setApproved(0);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('message.rejected_entry_successfully'));
        return $this->redirectToRoute('app_admin_entry_show', [
                    'uuid' => $gb->getUuid()
                        ], Response::HTTP_SEE_OTHER);
    }
}
