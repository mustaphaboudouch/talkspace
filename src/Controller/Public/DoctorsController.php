<?php

namespace App\Controller\Public;

use App\Entity\Appointment;
use App\Entity\User;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
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
            return $user->getRole() === 'ROLE_DOCTOR' && $user->isActive();
        });

        return $this->render('public/doctors/index.html.twig', [
            'doctors' => $doctors,
        ]);
    }

    #[Route('/doctors/{id}', name: 'app_doctor_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('IS_DOCTOR', $user);

        $daysOff = $user->getDaysOff();
        $appointments = $user->getDoctorAppointments();

        // Days Navigation

        $dateComponents = getdate();

        $day = isset($_GET["day"]) ? $_GET['day'] : $dateComponents['mday'];
        $month = isset($_GET["month"]) ? $_GET['month'] : $dateComponents['mon'];
        $year = isset($_GET["year"]) ? $_GET['year'] : $dateComponents['year'];

        $prevDay = date('d', mktime(0, 0, 0, $month, $day - 1, $year));
        $prevMonth = date('m', mktime(0, 0, 0, $month, $day - 1, $year));
        $prevYear = date('Y', mktime(0, 0, 0, $month, $day - 1, $year));
        $nextDay = date('d', mktime(0, 0, 0, $month, $day + 1, $year));
        $nextMonth = date('m', mktime(0, 0, 0, $month, $day + 1, $year));
        $nextYear = date('Y', mktime(0, 0, 0, $month, $day + 1, $year));

        // Time slots

        $currentDay = new DateTime("$day-$month-$year");
        $dayName = strtoupper($currentDay->format('l'));

        $scheduleStartTime = null;
        $scheduleEndTime = null;

        foreach ($user->getSchedules() as $s) {
            if ($s->getDay() == $dayName) {
                $scheduleStartTime = $s->getStartTime();
                $scheduleEndTime = $s->getEndTime();
            }
        }

        // filter by days off
        foreach ($daysOff as $dayOff) {
            if ($dayOff->getDate() == $currentDay) {
                $scheduleEndTime = $scheduleStartTime;
                break;
            }
        }

        // filter by old appointments
        $filteredAppointments = [];
        foreach ($appointments as $appointment) {
            if ($appointment->getDate() == $currentDay) {
                $filteredAppointments[] = $appointment;
            }
        }

        $slots = $this->getTimeSlots(
            $scheduleStartTime,
            $scheduleEndTime,
            $user->getAccount()->getDuration(),
            $user->getAccount()->getCleanup(),
            $filteredAppointments
        );

        return $this->render('public/doctors/show.html.twig', [
            'doctor' => $user,
            'slots' => $slots,
            'currentDay' => $day . "-" . $month . "-" . $year,
            'nextLink' => "?day=" . $nextDay . "&month=" . $nextMonth . "&year=" . $nextYear,
            'prevLink' => "?day=" . $prevDay . "&month=" . $prevMonth . "&year=" . $prevYear,
            'currentLink' => "?day=" . date('d') . "&month=" . date('m') . "&year=" . date('Y'),
        ]);
    }


    #[Route('/doctors/{id}/pick', name: 'app_doctor_appointment_pick', methods: ['GET', 'POST'])]
    public function pick(
        User $user,
        Request $request,
        ServiceRepository $serviceRepository,
        AppointmentRepository $appointmentRepository
    ): Response {
        $loggedUser = $this->getUser();
        $this->denyAccessUnlessGranted('IS_DOCTOR', $user);
        $this->denyAccessUnlessGranted('USER_ACTIVE', $loggedUser);

        $daysOff = $user->getDaysOff();
        $appointments = $user->getDoctorAppointments();

        // Days Navigation

        $dateComponents = getdate();

        $day = isset($_GET["day"]) ? $_GET['day'] : $dateComponents['mday'];
        $month = isset($_GET["month"]) ? $_GET['month'] : $dateComponents['mon'];
        $year = isset($_GET["year"]) ? $_GET['year'] : $dateComponents['year'];

        // Time slots

        $currentDay = new DateTime("$day-$month-$year");
        $dayName = strtoupper($currentDay->format('l'));

        $scheduleStartTime = null;
        $scheduleEndTime = null;

        foreach ($user->getSchedules() as $s) {
            if ($s->getDay() == $dayName) {
                $scheduleStartTime = $s->getStartTime();
                $scheduleEndTime = $s->getEndTime();
            }
        }

        // filter by days off
        foreach ($daysOff as $dayOff) {
            if ($dayOff->getDate() == $currentDay) {
                $scheduleEndTime = $scheduleStartTime;
                break;
            }
        }

        // filter by old appointments
        $filteredAppointments = [];
        foreach ($appointments as $appointment) {
            if ($appointment->getDate() == $currentDay) {
                $filteredAppointments[] = $appointment;
            }
        }

        $slots = $this->getTimeSlots(
            $scheduleStartTime,
            $scheduleEndTime,
            $user->getAccount()->getDuration(),
            $user->getAccount()->getCleanup(),
            $filteredAppointments
        );

        $selectedSlot = $request->get('slot');
        $times = explode("-", $selectedSlot);
        $selectedStartTime = new DateTime($times[0]);
        $selectedEndTime = new DateTime($times[1]);

        $selectedDate = new DateTimeImmutable($request->get('date'));
        $selectedService = $serviceRepository->find($request->get('service'));

        if ($selectedDate && $selectedSlot && $selectedService && in_array($selectedSlot, $slots)) {
            $appointment = new Appointment();
            $appointment->setDate($selectedDate);
            $appointment->setPatient($this->getUser());
            $appointment->setDoctor($user);
            $appointment->setService($selectedService);
            $appointment->setStartTime($selectedStartTime);
            $appointment->setEndTime($selectedEndTime);

            $appointmentRepository->save($appointment, true);
            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('public/doctors/show.html.twig', [
            'doctor' => $user,
        ]);
    }


    public function getTimeSlots($startTime, $endTime, $duration, $cleanup, $appointments)
    {
        $start = $startTime;
        $end = $endTime;
        $interval = new DateInterval('PT' . $duration . 'M');
        $cleanupInterval = new DateInterval('PT' . $cleanup . 'M');

        $slots = array();

        for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
            $endPeriod = clone $intStart;
            $endPeriod->add($interval);

            if ($endPeriod > $end) {
                break;
            }

            $isBooked = false;
            foreach ($appointments as $appointment) {
                if ($appointment->getStartTime() <= $intStart && $appointment->getEndTime() >= $endPeriod) {
                    $isBooked = true;
                }
            }

            if (!$isBooked) {
                $slots[] = $intStart->format("H:i") . "-" . $endPeriod->format("H:i");
            }
        }

        return $slots;
    }
}
