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
use App\Utils\Paginator;
use App\Service\FileUploader;
use App\Entity\GB;
use App\Form\{
    AttachType,
};

#[Route('/admin')]
class AdminController extends AbstractController
{

    private const PUBLIC_ATTACMENTS_DIR = '/public/attachments/entry/';
    private const ACCESS_DENIED = 'You don\'t have permission to access to this resource.';

    /**
     * 
     * @param int|null $objectId
     * @return string
     */
    private function getTargetDir(?int $objectId): string
    {
        return $this->getParameter('kernel.project_dir') . self::PUBLIC_ATTACMENTS_DIR . $objectId;
    }

    #[Route('/', name: 'app_admin_index')]
    #[Route('/order/by/{name}/{order}', name: 'app_admin_sort_entries')]
    public function index(Request $request, Paginator $paginator, GBRepository $gbRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

        $name = $request->get('name');
        $order = $request->get('order');

        $orderBy = ($order == 'asc' ? 'asc' : 'desc');

        $query = $gbRepository->findAllEntries($orderBy, $name);

        return $this->render('admin/gb/index.html.twig', [
                    'orderBy' => ($request->get('order') == 'asc' ? 'desc' : 'asc'),
                    'paginator' => $paginator->paginate($query, $request->query->getInt('page', 1)),
        ]);
    }

    #[Route('/{uuid}', name: 'app_admin_entry_show', methods: ['GET'])]
    public function show(GB $gb): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

        return $this->render('admin/gb/show.html.twig', [
                    'gb' => $gb,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_admin_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GB $gb, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

        $form = $this->createForm(AttachType::class, $gb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been updated successfuly.');
            $entityManager->flush();

            $file = $form->get('name')->getData();

            if ($file) {
                $fileUploader = new FileUploader($this->getTargetDir($gb->getId()), $slugger);

                try {
                    $attach = $fileUploader->upload($file)->handle();
                } catch (\Exception $ex) {
                    throw new \Exception($ex->getMessage());
                }

                $attach->setGb($gb);

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, self::ACCESS_DENIED);

        $gb->setApproved(0);
        $entityManager->flush();

        $this->addFlash('success', $translator->trans('message.rejected_entry_successfully'));
        return $this->redirectToRoute('app_admin_entry_show', [
                    'uuid' => $gb->getUuid()
                        ], Response::HTTP_SEE_OTHER);
    }
}
