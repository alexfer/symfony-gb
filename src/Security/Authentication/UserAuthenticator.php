<?php

namespace App\Security\Authentication;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Doctrine\ORM\EntityManagerInterface;

class UserAuthenticator extends AbstractAuthenticator
{

    private UserRepository $userRepository;
    private RouterInterface $router;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/login' && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');
        $token = $request->request->get('_csrf_token');

        return new Passport(
                new UserBadge($email, function ($userIdentifier) use ($request) {
                            // optionally pass a callback to load the User manually
                            $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                            if (!$user) {
                                throw new UserNotFoundException();
                            }
                            $user->setIp($request->getClientIp());
                            $this->entityManager->persist($user);
                            $this->entityManager->flush();
                            return $user;
                        }),
                new PasswordCredentials($password), [new CsrfTokenBadge('authenticate', $token), new RememberMeBadge(),]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
                $this->router->generate('homepage')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse(
                $this->router->generate('app_login')
        );
    }
}
