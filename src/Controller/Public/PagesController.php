<?php

namespace App\Controller\Public;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('public/pages/home.html.twig');
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('public/pages/about.html.twig');
    }

    #[Route('/support', name: 'app_support', methods: ['GET', 'POST'])]
    public function contact(Request $request, ContactRepository $contactRepository): Response
    {
        $contactForm = $this->createForm(ContactFormType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $contact = $contactForm->getData();
            $contactRepository->save($contact, true);

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('public/pages/support.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }

    #[Route('/privacy-policy', name: 'app_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('public/pages/privacy_policy.html.twig');
    }

    #[Route('/terms-of-service', name: 'app_terms_of_service')]
    public function termsOfService(): Response
    {
        return $this->render('public/pages/terms_of_service.html.twig');
    }
}
