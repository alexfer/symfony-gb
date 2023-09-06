<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Paginator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Form\User\FormType;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/manage/user')]
class UserController extends AbstractController
{

    /**
     * 
     * @param Request $request
     * @param Paginator $paginator
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/', name: 'app_admin_users')]
    #[Route('/order/by/{name}/{order}', name: 'app_admin_sort_users')]
    public function index(
            Request $request,
            Paginator $paginator,
            UserRepository $userRepository,
    ): Response
    {
        $name = $request->get('name');
        $order = $request->get('order');

        $orderBy = ($order == 'asc' ? 'asc' : 'desc');

        $query = $userRepository->findAllUsers($orderBy, $name);

        return $this->render('admin/user/index.html.twig', [
                    'orderBy' => ($request->get('order') == 'asc' ? 'desc' : 'asc'),
                    'paginator' => $paginator->paginate($query, $request->query->getInt('page', 1)),
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(
            Request $request,
            UserPasswordHasherInterface $userPasswordHasher,
            EntityManagerInterface $entityManager,
    ): Response
    {
        $user = new User();

        $form = $this->createForm(FormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'User has been created successfuly.');

            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('password')->getData()
                    )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_edit', [
                        'id' => $user->getId(),
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
                    'user' => $user,
                    'form' => $form,
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(
            Request $request,
            User $user,
            EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(FormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'User has been updated successfuly.');
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_edit', [
                        'action' => 'edit',
                        'id' => $user->getId(),
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form,
        ]);
    }

    /**
     * 
     * @param User $user
     * @return Response
     */
    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
                    'user' => $user,
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}/trash', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(
            Request $request,
            User $user,
            EntityManagerInterface $entityManager,
            TranslatorInterface $translator,
    ): Response
    {
        
    }
}
