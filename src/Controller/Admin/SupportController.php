<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    #[Route('/admin/support', name: 'app_admin_support_index')]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('admin/support/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

    #[Route('/admin/support/{id}', name: 'app_admin_support_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('admin/support/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/admin/support/{id}/edit', name: 'app_admin_support_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->save($contact, true);

            return $this->redirectToRoute('app_admin_support_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/support/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/admin/support/{id}', name: 'app_admin_support_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact, true);
        }

        return $this->redirectToRoute('app_admin_support_index', [], Response::HTTP_SEE_OTHER);
    }
}
