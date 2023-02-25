<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ResponseFormType;
use App\Repository\ContactRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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

    #[Route('/admin/support/{id}', name: 'app_admin_support_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Contact $contact,
        ContactRepository $contactRepository,
        MailerInterface $mailer,
    ): Response {
        $form = $this->createForm(ResponseFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject = $form->get('subject')->getData();
            $response = $form->get('response')->getData();

            // send response email
            $email = (new TemplatedEmail())
                ->from(new Address('support@talkspace.fr', 'TalkSpace'))
                ->to($contact->getEmail())
                ->subject($subject)
                ->htmlTemplate('emails/support_email.html.twig')
                ->context([
                    'response' => $response,
                ]);
            $mailer->send($email);

            // delete message
            $contactRepository->remove($contact, true);

            return $this->redirectToRoute('app_admin_support_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/support/show.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
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
