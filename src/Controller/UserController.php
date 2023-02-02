<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[IsGranted('ROLE_ADMIN')]
#[Route('/user', name: 'app_user_')]
class UserController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $userRepository->search($request, $request->query->getInt('limit',10)),
        ]);
    }

    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'], methods: ['GET','POST'])]
    public function update(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/remove/{token}', name: 'remove', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function remove(User $user, string $token, UserRepository $userRepository): Response
    {
        if (!$this->isCsrfTokenValid('remove' . $user->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $userRepository->remove($user, true);

        return $this->redirectToRoute('app_user_index');
    }
}
