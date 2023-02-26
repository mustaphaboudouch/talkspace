<?php

namespace App\Controller\Doctor;

use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/doctor/services', name: 'app_doctor_service_index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('doctor/service/index.html.twig', [
            'services' => $user->getServices(),
        ]);
    }

    #[Route('/doctor/services/new', name: 'app_doctor_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $service->setDoctor($user);
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_doctor_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('doctor/service/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/doctor/services/{id}/edit', name: 'app_doctor_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('USER_ACTIVE', $user);

        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);

            return $this->redirectToRoute('app_doctor_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('doctor/service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/doctor/services/{id}', name: 'app_doctor_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('USER_ACTIVE', $user);

        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $serviceRepository->remove($service, true);
        }

        return $this->redirectToRoute('app_doctor_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
