<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
