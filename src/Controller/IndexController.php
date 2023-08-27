<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\{
    GBRepository,
    CommentRepository,
};

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(GBRepository $gBRepository, CommentRepository $commentRepository, UserInterface $user): Response
    {
        return $this->render('index/index.html.twig', [
                    'entries' => $gBRepository->findBy(['approved' => true], ['id' => 'DESC']),
                    'comments' => $commentRepository->findBy([], ['id' => 'DESC']),                    
        ]);
    }
}
