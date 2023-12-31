<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\User\LoginType;

class SrcurityController extends AbstractController
{

    /**
     * 
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login')]
    public function login(
            Request $request,
            AuthenticationUtils $authenticationUtils,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $defaultData = ['username' => $authenticationUtils->getLastUsername()];

        $form = $this->createForm(LoginType::class, $defaultData);
        $form->handleRequest($request);

        return $this->render('auth/login.html.twig', [
                    'loginForm' => $form->createView(),
                    'last_username' => $authenticationUtils->getLastUsername(),
                    'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * 
     * @throws \Exception
     */
    #[Route('/login/failed', name: 'app_login_failed')]
    public function error()
    {
        throw new \Exception('Please confirm your email');
    }

    /**
     * 
     * @return Response
     * @throws \Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
