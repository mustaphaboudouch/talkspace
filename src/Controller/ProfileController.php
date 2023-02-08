<?php

namespace App\Controller;

use App\Form\ProfileDeleteFormType;
use App\Form\ProfilePasswordFormType;
use App\Form\ProfilePersonalFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();

        $personalForm = $this->createForm(ProfilePersonalFormType::class, $user);
        $personalForm->handleRequest($request);

        $passwordForm = $this->createForm(ProfilePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        $deleteForm = $this->createForm(ProfileDeleteFormType::class, $user);
        $deleteForm->handleRequest($request);

        // personal infos form
        if ($personalForm->isSubmitted() && $personalForm->isValid()) {
            $user = $personalForm->getData();
            $userRepository->save($user, true);

            $this->addFlash('success', 'Votre informations personnelles ont bien été modifiées.');
            return $this->redirectToRoute('app_profile');
        }

        // password update form
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            if ($passwordHasher->isPasswordValid($user, $passwordForm->get('plainPassword')->getData())) {
                // encode the plain password
                $encodedPassword = $passwordHasher->hashPassword(
                    $user,
                    $passwordForm->get('newPassword')->getData()
                );

                // update current password
                $user->setPassword($encodedPassword);
                $userRepository->save($user, true);

                // logout user
                $session = new Session();
                $session->invalidate();

                // TODO: add flash message
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            return $this->redirectToRoute('app_profile');
        }

        // account deletion form
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            if ($passwordHasher->isPasswordValid($user, $deleteForm->get('plainPassword')->getData())) {
                // TODO: add soft delete
                $userRepository->remove($user, true);

                // logout user
                $session = new Session();
                $session->invalidate();

                // TODO: add flash message
                return $this->redirectToRoute('app_home');
            }

            $this->addFlash('error', 'Le mot de passe est incorrect.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profilePersonalForm' => $personalForm,
            'profilePasswordForm' => $passwordForm,
            'profileDeleteForm' => $deleteForm,
        ]);
    }
}
