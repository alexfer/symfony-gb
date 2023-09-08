<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
    BinaryFileResponse,
};
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\CommentType;
use App\Entity\{
    GB,
    Comment,
    Attach,
};
use App\Repository\{
    GBRepository,
    CommentRepository,
};

class IndexController extends AbstractController
{

    /**
     * 
     * @param GBRepository $gbRepository
     * @param CommentRepository $commentRepository
     * @return Response
     */
    #[Route('/index', name: 'app_index')]
    public function index(
            GBRepository $gbRepository,
            CommentRepository $commentRepository,
    ): Response
    {
        return $this->render('index/index.html.twig', [
                    'entries' => $gbRepository->findBy(['approved' => true], ['id' => 'DESC'], 5),
                    'comments' => $commentRepository->findBy(['approved' => true], ['id' => 'DESC'], 5),
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param GB $gb
     * @param CommentRepository $commentRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/show/{uuid}', name: 'app_entry_show', methods: ['GET', 'POST'])]
    public function show(
            Request $request,
            GB $gb,
            CommentRepository $commentRepository,
            EntityManagerInterface $entityManager,
    ): Response
    {
        $comment = new Comment();
        $comment->setGb($gb);

        $token = $this->container->get('security.token_storage')->getToken();
        $condition = $token !== null ? ['gb_id' => $gb->getId()] : ['gb_id' => $gb->getId(), 'approved' => true];
        
        $totalComments = $commentRepository->countComments($comment->getGb()->getId());
        
        if($token !== null) {
            $totalComments = $commentRepository->countAllComments($comment->getGb()->getId());
        }

        $comments = $commentRepository->findBy($condition, ['created_at' => 'DESC'], 10);

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
                    'gb' => $gb,
                    'totalComments' => $totalComments,
                    'comments' => $comments,
        ]);
    }

    /**
     * 
     * @param Comment $comment
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/publish-comment/{id}', name: 'app_comment_publish', methods: ['GET'])]
    public function publish(
            Comment $comment,
            UserInterface $user,
            EntityManagerInterface $entityManager,
            TranslatorInterface $translator,
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

    /**
     * 
     * @param Request $request
     * @param Attach $attach
     * @return Response
     */
    #[Route('/{uuid}/download-file/{id}', name: 'app_download_file', methods: ['GET'])]
    public function download(Request $request, Attach $attach): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/attachments/entry/' . $attach->getGbId() . '/' . $attach->getName();
        if (file_exists($filePath)) {
            $response = new BinaryFileResponse($filePath);
            return $response;
        }
        return $this->redirectToRoute('app_entry_show', ['uuid' => $attach->getGb()->getUuid()], Response::HTTP_SEE_OTHER);
    }

    /**
     * 
     * @param Comment $comment
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/delete-comment/{id}', name: 'app_comment_delete', methods: ['GET'])]
    public function delete(
            Comment $comment,
            UserInterface $user,
            EntityManagerInterface $entityManager,
            TranslatorInterface $translator,
    ): Response
    {
        if ($comment->getGb()->getUserId() != $user->getId()) {
            $this->denyAccessUnlessGranted($user->getRoles(), $comment, $translator->trans('http_error_403.description'));
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_entry_show', [
                    'uuid' => $comment->getGb()->getUuid(),
                        ], Response::HTTP_SEE_OTHER);
    }
}
