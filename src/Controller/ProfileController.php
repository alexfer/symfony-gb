<?php

namespace App\Controller;

use App\Form\User\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\{
    Response,
    Request,
};
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    /**
     * 
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
            Request $request,
            UserInterface $user,
            UserPasswordHasherInterface $userPasswordHasher,
            EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setEmail($user->getEmail());
            $user->setName($form->get('name')->getData());
            $pasword = $form->get('plainPassword');

            $user->setPassword($userPasswordHasher->hashPassword($user, $pasword->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'You profile has been updated successfuly.');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/index.html.twig', [
                    'user' => $user,
                    'form' => $form,
        ]);
    }
}
