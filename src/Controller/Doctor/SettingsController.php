<?php

namespace App\Controller\Doctor;

use App\Entity\DayOff;
use App\Entity\Period;
use App\Form\DayOffFormType;
use App\Form\PeriodFormType;
use App\Form\SettingsAccountFormType;
use App\Repository\AccountRepository;
use App\Repository\DayOffRepository;
use App\Repository\PeriodRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        PeriodRepository $periodRepository,
    ): Response {
        $user = $this->getUser();

        $account = $user->getAccount();
        $accountForm = $this->createForm(SettingsAccountFormType::class, $account);
        $accountForm->handleRequest($request);

        $schedules = $user->getSchedules();
        $periodForm = $this->createForm(PeriodFormType::class);
        $periodForm->handleRequest($request);

        $daysOff = $user->getDaysOff();
        $dayOffForm = $this->createForm(DayOffFormType::class);
        $dayOffForm->handleRequest($request);

        // account infos form
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $account = $accountForm->getData();
            $accountRepository->save($account, true);

            $this->addFlash('success', 'Votre informations ont bien été modifiées.');
            return $this->redirectToRoute('app_doctor_settings');
        }

        // schedule form
        if ($periodForm->isSubmitted() && $periodForm->isValid()) {
            $selectedSchedule = null;
            foreach ($user->getSchedules() as $schedule) {
                if ($schedule->getDay() === $periodForm->get('schedule')->getData()) {
                    $selectedSchedule = $schedule;
                    break;
                }
            }

            $period = $periodForm->getData();
            $period->setSchedule($selectedSchedule);
            $periodRepository->save($period, true);

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
            'periodForm' => $periodForm->createView(),
            'dayOffForm' => $dayOffForm->createView(),
        ]);
    }

    #[Route('/doctor/settings/day-off/{id}', name: 'app_doctor_settings_dayoff_delete', methods: ['POST'])]
    public function deleteDayOff(Request $request, DayOff $dayOff, DayOffRepository $dayOffRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dayOff->getId(), $request->request->get('_token'))) {
            $dayOffRepository->remove($dayOff, true);
        }

        return $this->redirectToRoute('app_doctor_settings', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/doctor/settings/period/{id}', name: 'app_doctor_settings_period_delete', methods: ['POST'])]
    public function deletePeriod(Request $request, Period $period, PeriodRepository $periodRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
            $periodRepository->remove($period, true);
        }

        return $this->redirectToRoute('app_doctor_settings', [], Response::HTTP_SEE_OTHER);
    }
}
