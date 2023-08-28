<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
                    'comments' => $commentRepository->findBy([], ['id' => 'DESC'], 5),
        ]);
    }

    #[Route('/get/{uuid}', name: 'app_entry_show', methods: ['GET'])]
    public function get(GB $gB): Response
    {
        return $this->render('index/show.html.twig', [
                    'gb' => $gB,
                    'comments' => $gB->getComments(),
        ]);
    }

    #[Route('/publish-comment/{id}', name: 'app_comment_publish', methods: ['GET'])]
    public function publish(GB $gB, Comment $comment, UserInterface $user, TranslatorInterface $translator): Response
    {
        if ($comment->getGb()->getUserId() != $user->getId()) {
            $this->denyAccessUnlessGranted($user->getRoles(), $comment, $translator->trans('http_error_403.description'));
        }

        return $this->redirectToRoute('app_entry_show', [
                    'uuid' => $comment->getGb()->getUuid(),
                        ], Response::HTTP_SEE_OTHER);
    }
}
