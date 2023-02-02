<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilPatientController extends AbstractController
{
    #[Route('/profil/patient', name: 'app_profil_patient')]
    public function index(): Response
    {
        return $this->render('profil_patient/index.html.twig', [
            'controller_name' => 'ProfilPatientController',
        ]);
    }
}
