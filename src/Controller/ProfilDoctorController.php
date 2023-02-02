<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilDoctorController extends AbstractController
{
    #[Route('/profil/doctor', name: 'app_profil_doctor')]
    public function index(): Response
    {
        return $this->render('profil_doctor/index.html.twig', [
            'controller_name' => 'ProfilDoctorController',
        ]);
    }
}
