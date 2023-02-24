<?php

namespace App\Controller\Public;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorsController extends AbstractController
{
    #[Route('/doctors', name: 'app_doctor_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $doctors = array_filter($users, function ($user) {
            return $user->getRole() === 'ROLE_DOCTOR';
        });

        return $this->render('public/doctors/index.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/doctors/{id}', name: 'app_doctor_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('public/doctors/show.html.twig', [
            'doctor' => $user,
        ]);
    }
}
