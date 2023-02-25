<?php

namespace App\Controller\Shared;

use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepository,
        ServiceRepository $serviceRepository,
        AppointmentRepository $appointmentRepository
    ): Response {
        $doctors = [];
        $patients = [];
        $services = [];
        $appointments = [];

        $user = $this->getUser();

        if ($user->getRole() === 'ROLE_ADMIN') {
            $doctors = $userRepository->findAll();
            $patients = $userRepository->findAll();
            $services = $serviceRepository->findAll();
            $appointments = $appointmentRepository->findAll();
        }

        if ($user->getRole() === 'ROLE_DOCTOR') {
            $patients = $userRepository->findAll();
            $services = $user->getServices();
            $appointments = $appointmentRepository->findAll();
        }

        if ($user->getRole() === 'ROLE_PATIENT') {
            $appointments = $appointmentRepository->findAll();
        }

        return $this->render('dashboard/index.html.twig', [
            'doctorsCount' => count($doctors),
            'patientsCount' => count($patients),
            'servicesCount' => count($services),
            'appointmentsCount' => count($appointments),
        ]);
    }
}
