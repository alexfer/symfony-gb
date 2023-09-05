<?php

namespace App\Controller;

use App\Entity\{
    GB,
    Attach,
};
use App\Form\{
    GBType,
    AttachType,
};
use App\Repository\GBRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Utils\Paginator;
use App\Service\FileUploader;

#[Route('/gb')]
class GBController extends AbstractController
{

    const PUBLIC_ATTACMENTS_DIR = '/public/attachments/entry/';

    #[Route('/', name: 'app_gb_index', methods: ['GET'])]
    ##[Route('/order/by/{name}/{order}', name: 'app_gb_sort_entries')]
    public function index(Request $request, Paginator $paginator, GBRepository $gbRepository, UserInterface $user): Response
    {
        $name = $request->get('name');
        $order = $request->get('order');

        $orderBy = ($order == 'asc' ? 'asc' : 'desc');

        $query = $gbRepository->findAllEntriesByUserId($user->getId());

        return $this->render('gb/index.html.twig', [
                    'orderBy' => ($request->get('order') == 'asc' ? 'desc' : 'asc'),
                    'paginator' => $paginator->paginate($query, $request->query->getInt('page', 1)),
        ]);
    }

    /**
     * 
     * @param int|null $objectId
     * @return string
     */
    private function getTargetDir(?int $objectId): string
    {
        return $this->getParameter('kernel.project_dir') . self::PUBLIC_ATTACMENTS_DIR . $objectId;
    }

    #[Route('/new', name: 'app_gb_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserInterface $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $gb = new GB();

        $form = $this->createForm(AttachType::class, $gb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been created successfuly.');

            $uuid = Uuid::v4();
            $gb->setUuid($uuid)->setUser($user)->setApproved(0);

            $entityManager->persist($gb);
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

            return $this->redirectToRoute('app_gb_edit', [
                        'uuid' => $uuid
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/new.html.twig', [
                    'gb' => $gb,
                    'form' => $form,
        ]);
    }

    #[Route('/{uuid}', name: 'app_gb_show', methods: ['GET'])]
    public function show(GB $gb): Response
    {
        return $this->render('gb/show.html.twig', [
                    'gb' => $gb,
        ]);
    }

    #[Route('/{uuid}/edit', name: 'app_gb_edit', methods: ['GET', 'POST'])]
    public function edit(
            Request $request,
            GB $gb,
            EntityManagerInterface $entityManager,
            SluggerInterface $slugger,
    ): Response
    {
        $form = $this->createForm(AttachType::class, $gb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

            $this->addFlash('success', 'Entry has been updated successfuly.');
            $entityManager->flush();

            return $this->redirectToRoute('app_gb_edit', [
                        'uuid' => $gb->getUuid()
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('gb/edit.html.twig', [
                    'gb' => $gb,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gb_delete', methods: ['POST'])]
    public function delete(Request $request, GB $gb, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gb->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gb);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gb_index', [], Response::HTTP_SEE_OTHER);
    }
}
