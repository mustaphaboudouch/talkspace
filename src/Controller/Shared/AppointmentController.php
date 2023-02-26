<?php

namespace App\Controller\Shared;

use App\Entity\Appointment;
use App\Entity\File;
use App\Form\AppointmentFileFormType;
use App\Repository\AppointmentRepository;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->getUser();

        $appointments = [];

        if ($user->getRole() === 'ROLE_ADMIN') {
            $appointments = $appointmentRepository->findAll();
        }
        if ($user->getRole() === 'ROLE_DOCTOR') {
            $appointments = $user->getDoctorAppointments();
        }
        if ($user->getRole() === 'ROLE_PATIENT') {
            $appointments = $user->getAppointments();
        }

        return $this->renderForm('appointment/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('/appointment/{id}', name: 'app_appointment_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Appointment $appointment,
        FileRepository $fileRepository,
        SluggerInterface $slugger
    ): Response {
        $user = $this->getUser();

        $appointmentFile = new File();
        $appointmentFileForm = $this->createForm(AppointmentFileFormType::class);
        $appointmentFileForm->handleRequest($request);

        if ($appointmentFileForm->isSubmitted() && $appointmentFileForm->isValid()) {
            $appointmentFile = $appointmentFileForm->getData();

            $appointmentFileUrl = $appointmentFileForm->get('file')->getData();
            if ($appointmentFileUrl) {
                // generate new unique filename
                $originalFilename = pathinfo($appointmentFileUrl->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $appointmentFileUrl->guessExtension();
                // move the file to `appointment_files_directory` directory
                try {
                    $appointmentFileUrl->move(
                        $this->getParameter('appointment_files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de fichier.');
                }
                $appointmentFile->setUrl($newFilename);
            }

            $appointmentFile->setSender($user);
            $appointmentFile->setAppointment($appointment);

            $fileRepository->save($appointmentFile, true);

            $this->addFlash('success', 'Fichier ajouté avec succès.');
        }

        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
            'appointmentFileForm' => $appointmentFileForm->createView(),
        ]);
    }

    #[Route('/appointment/{id}', name: 'app_appointment_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Appointment $appointment,
        AppointmentRepository $appointmentRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $appointment->getId(), $request->request->get('_token'))) {
            $appointmentRepository->remove($appointment, true);
        }

        return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/appointment/files/{id}', name: 'app_appointment_file_delete', methods: ['POST'])]
    public function deleteFile(
        Request $request,
        File $file,
        FileRepository $fileRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            $fileRepository->remove($file, true);
        }

        return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
    }
}
