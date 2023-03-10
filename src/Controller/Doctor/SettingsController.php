<?php

namespace App\Controller\Doctor;

use App\Entity\DayOff;
use App\Form\DayOffFormType;
use App\Form\ScheduleFormType;
use App\Form\SettingsAccountFormType;
use App\Repository\AccountRepository;
use App\Repository\DayOffRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/doctor/settings', name: 'app_doctor_settings')]
    public function index(
        Request $request,
        AccountRepository $accountRepository,
        DayOffRepository $dayOffRepository,
        UserRepository $userRepository,
        ScheduleRepository $scheduleRepository,
    ): Response {
        $user = $this->getUser();

        $account = $user->getAccount();
        $accountForm = $this->createForm(SettingsAccountFormType::class, $account);
        $accountForm->handleRequest($request);

        $schedules = $user->getSchedules();
        $scheduleForm = $this->createForm(ScheduleFormType::class);
        $scheduleForm->handleRequest($request);

        $daysOff = $user->getDaysOff();
        $dayOffForm = $this->createForm(DayOffFormType::class);
        $dayOffForm->handleRequest($request);

        // account infos form
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $account = $accountForm->getData();
            $accountRepository->save($account, true);

            $this->addFlash('success', 'Vos informations ont bien été modifiées.');
            return $this->redirectToRoute('app_doctor_settings');
        }

        // schedule form
        if ($scheduleForm->isSubmitted() && $scheduleForm->isValid()) {
            $selectedSchedule = null;
            foreach ($user->getSchedules() as $schedule) {
                if ($schedule->getDay() === $scheduleForm->get('schedule')->getData()) {
                    $selectedSchedule = $schedule;
                    break;
                }
            }

            $schedule = $scheduleForm->getData();
            $schedule->setDay($selectedSchedule->getDay());
            $schedule->setDoctor($user);
            $scheduleRepository->save($schedule, true);

            // remove existing one
            $scheduleRepository->remove($selectedSchedule, true);

            $this->addFlash('success', 'Votre calendrier a été mis à jour avec succès.');
            return $this->redirectToRoute('app_doctor_settings');
        }

        // days off form
        if ($dayOffForm->isSubmitted() && $dayOffForm->isValid()) {
            $dayOff = $dayOffForm->getData();
            $dayOff->setDoctor($user);
            $dayOffRepository->save($dayOff, true);

            $user->addDaysOff($dayOff);
            $userRepository->save($user, true);

            $this->addFlash('success', 'Le jour de congé a été ajouté avec succès.');
            return $this->redirectToRoute('app_doctor_settings');
        }

        return $this->render('doctor/settings/index.html.twig', [
            'schedules' => $schedules,
            'daysOff' => $daysOff,
            'accountForm' => $accountForm->createView(),
            'scheduleForm' => $scheduleForm->createView(),
            'dayOffForm' => $dayOffForm->createView(),
        ]);
    }

    #[Route('/doctor/settings/day-off/{id}', name: 'app_doctor_settings_dayoff_delete', methods: ['POST'])]
    public function deleteDayOff(Request $request, DayOff $dayOff, DayOffRepository $dayOffRepository): Response
    {
        $this->denyAccessUnlessGranted('DAY_OFF_EDIT', $dayOff);

        if ($this->isCsrfTokenValid('delete' . $dayOff->getId(), $request->request->get('_token'))) {
            $dayOffRepository->remove($dayOff, true);
        }

        return $this->redirectToRoute('app_doctor_settings', [], Response::HTTP_SEE_OTHER);
    }
}
