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
        $users = [];
        $services = [];
        $appointments = [];

        $user = $this->getUser();

        if ($user->getRole() === 'ROLE_ADMIN') {
            $users = $userRepository->findAll();
            $services = $serviceRepository->findAll();
            $appointments = $appointmentRepository->findAll();
        }

        if ($user->getRole() === 'ROLE_DOCTOR') {
            $services = $user->getServices();
            $appointments = $user->getDoctorAppointments();
        }

        if ($user->getRole() === 'ROLE_PATIENT') {
            $appointments = $user->getAppointments();
        }

        return $this->render('dashboard/index.html.twig', [
            'usersCount' => count($users),
            'patientsCount' => count($users),
            'servicesCount' => count($services),
            'appointmentsCount' => count($appointments),
        ]);
    }
}
