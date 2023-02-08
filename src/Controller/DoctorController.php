<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends AbstractController
{
    #[Route('/doctors', name: 'app_doctor_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('doctors/index.html.twig');
    }

    #[Route('/doctors/{id}', name: 'app_doctor_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('doctors/show.html.twig', [
            'user' => $user,
        ]);
    }
}
