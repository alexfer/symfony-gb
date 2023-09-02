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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function new(Request $request, UserInterface $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $gB = new GB();

        $form = $this->createForm(AttachType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Entry has been created successfuly.');

            $uuid = Uuid::v4();
            $gB->setUuid($uuid);
            $gB->setUser($user);
            $gB->setApproved(0);

            $entityManager->persist($gB);
            $entityManager->flush();

            $source = $form->get('name')->getData();

            if ($source) {
                $originalFilename = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = sprintf("%s-%s.%s", $safeFilename, uniqid(), $source->guessExtension());

                try {
                    $source->move(
                            $info = $this->getParameter('kernel.project_dir') . '/public/attachments/entry/' . $gB->getId(),
                            $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }

                $attach = new Attach();
                $attach->setGB($gB);
                $attach->setName($newFilename)
                        ->setGB($gB)
                        ->setSize(filesize($info))
                        ->setMime(mime_content_type($info . '/' . $newFilename));

                $entityManager->persist($attach);
                $entityManager->flush();
            }

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
    public function edit(Request $request, GB $gB, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AttachType::class, $gB);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $source = $form->get('name')->getData();

            if ($source) {
                $originalFilename = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
                //dd($source->guessExtension());

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = sprintf("%s-%s.%s", $safeFilename, uniqid(), $source->guessExtension());

                try {
                    $source->move(
                            $info = $this->getParameter('kernel.project_dir') . '/public/attachments/entry/' . $gB->getId(),
                            $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }

                $attach = new Attach();
                $attach->setName($newFilename)
                        ->setGB($gB)
                        ->setSize(filesize($info))
                        ->setMime(mime_content_type($info . '/' . $newFilename));

                $entityManager->persist($attach);
                $entityManager->flush();
            }

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
