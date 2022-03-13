<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'app_user_')]
//#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    private UserPasswordHasherInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $encoder
    ) {
          $this->userRepository = $userRepository;
          $this->entityManager  = $entityManager;
          $this->encoder        = $encoder;
    }

    #[Route('/list', name: 'list')]
    public function listAction(Request $request): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/register', name: 'register')]
    public function createAction(Request $request): Response
    {
        $user = new User();

        return $this->handleFormRequest($request, $user);
    }

    #[Route('/enable/{userId}', name: 'enable')]
    public function enableAction(Request $request, int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            return $this->redirectToRoute('app_user_list');
        }

        $user->enable();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/disable/{userId}', name: 'disable')]
    public function disableAction(Request $request, int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            return $this->redirectToRoute('app_user_list');
        }

        $user->disable();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/edit/{userId}', name: 'edit')]
    public function editAction(Request $request, int $userId): Response
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            return $this->redirectToRoute('app_user_list');
        }

        return $this->handleFormRequest($request, $user);
    }

    #[Route('/delete/{userId}', name: 'delete')]
    public function deleteAction(Request $request, int $userId): Response
    {
        $user = $this->userRepository->find($userId);
        if ($user !== null) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('info', 'User deleted.');
        }

        $this->addFlash('error', 'User not found.');

        return $this->redirectToRoute('app_user_list');
    }

    private function handleFormRequest(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user     = $form->getData();
            $password = $user->getPassword();
            $password = $this->encoder->hashPassword($user, $password);

            $user->setPassword($password);
            $user->setCreatedAt(new DateTime('now', new DateTimeZone('UTC')));
            $user->setUpdatedAt(new DateTime('now', new DateTimeZone('UTC')));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('info', 'User saved.');

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render(
            'user/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
