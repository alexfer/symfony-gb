<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\CommentType;
use App\Entity\{
    GB,
    Comment,
};
use App\Repository\{
    GBRepository,
    CommentRepository,
};

class IndexController extends AbstractController
{

    #[Route('/', name: 'app_index')]
    public function index(GBRepository $gBRepository, CommentRepository $commentRepository): Response
    {
        return $this->render('index/index.html.twig', [
                    'entries' => $gBRepository->findBy(['approved' => true], ['id' => 'DESC'], 5),
                    'comments' => $commentRepository->findBy(['approved' => true], ['id' => 'DESC'], 5),
        ]);
    }

    #[Route('/show/{uuid}', name: 'app_entry_show', methods: ['GET', 'POST'])]
    public function show(
            Request $request,
            GB $gB,
            CommentRepository $commentRepository,
            UserInterface $user,
            EntityManagerInterface $entityManager
    ): Response
    {
        $comment = new Comment();
        $comment->setGb($gB);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Comment has been published successfuly.');

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_entry_show', [
                        'uuid' => $comment->getGb()->getUuid(),
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('index/show.html.twig', [
                    'comment_form' => $form->createView(),
                    'gb' => $gB,
                    'comments' => $user->getId() ? $commentRepository->findBy([], ['created_at' => 'DESC'], 10) : $commentRepository->findApproved($gB->getId()),
        ]);
    }

    #[Route('/publish-comment/{id}', name: 'app_comment_publish', methods: ['GET'])]
    public function publish(
            GB $gB,
            Comment $comment,
            UserInterface $user,
            EntityManagerInterface $entityManager,
            TranslatorInterface $translator
    ): Response
    {
        if ($comment->getGb()->getUserId() != $user->getId()) {
            $this->denyAccessUnlessGranted($user->getRoles(), $comment, $translator->trans('http_error_403.description'));
        }
        
        $comment->setApproved(1);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_entry_show', [
                    'uuid' => $comment->getGb()->getUuid(),
                        ], Response::HTTP_SEE_OTHER);
    }
}
