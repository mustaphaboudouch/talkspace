<?php

namespace App\Controller\Public;

use App\Entity\User;
use App\Form\AppointmentFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/doctors/{id}', name: 'app_doctor_show', methods: ['GET', 'POST'])]
    public function show(User $user, Request $request): Response
    {
        $duration = 15;

        $days = [];

        $schedules = $user->getSchedules();
        $daysOff = $user->getDaysOff();

        // dd($this->calculate($schedule->getPeriods()[0]->getEndTime(), $schedule->getPeriods()[0]->getStartTime()));

        foreach ($schedules as $schedule) {
            $periods = $schedule->getPeriods();
            foreach ($periods as $period) {
                if ($this->calculate($period->getEndTime(), $period->getStartTime()) >= $duration) {
                    //
                }
            }
        }

        $appointmentForm = $this->createForm(AppointmentFormType::class);
        $appointmentForm->handleRequest($request);

        if ($appointmentForm->isSubmitted() && $appointmentForm->isValid()) {
            //
        }

        return $this->render('public/doctors/show.html.twig', [
            'doctor' => $user,
            'appointmentForm' => $appointmentForm->createView(),
        ]);
    }

    public function calculate($time1, $time2)
    {
        $diff = $time1->diff($time2);
        $inM = intval($diff->format('%i'));
        $inH = intval($diff->format('%h'));

        return $inM + ($inH * 60);
    }
}
