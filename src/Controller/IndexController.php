<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(GBRepository $gBRepository): Response
    {
        return $this->render('gb/index.html.twig', [
                    'g_bs' => $gBRepository->findBy([], ['id' => 'DESC']),
        ]);
    }
}
