<?php

namespace App\Controller\Auth;

use App\Entity\Account;
use App\Entity\Schedule;
use App\Entity\Service;
use App\Entity\User;
use App\Form\RegisterFormType;
use App\Security\EmailVerifier;
use App\Security\LoginAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('roles')->getData();
            if ($role === 'ROLE_DOCTOR' || $role === 'ROLE_PATIENT') {
                $user->setRoles([$role]);
            } else {
                $this->addFlash('alert', 'Le rôle saisi est invalide.');
                return $this->render('auth/register.html.twig', [
                    'registerForm' => $form->createView(),
                ]);
            }

            if ($user->getRole() === 'ROLE_DOCTOR') {
                // add doctor account
                $account = new Account();
                $account->setDescription($user->getFirstname() . " " . $user->getLastname() . ", psychothérapeute diplômé, est un professionnel compatissant et dévoué avec 3 années d'expérience dans l'aide aux individus et aux familles à surmonter une variété de défis émotionnels et comportementaux. [Il/Elle] a obtenu son diplôme de Maîtrise en travail social de l'université Exemple et est autorisé à pratiquer la psychothérapie dans Paris.");
                $account->setExperience("Plus de 3 années d'expérience");
                $account->setDuration(15);
                $account->setCleanup(5);

                $entityManager->persist($account);
                $entityManager->flush();

                $user->setAccount($account);
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // persist user
            $entityManager->persist($user);
            $entityManager->flush();

            // add doctor schedule
            if ($user->getRole() === 'ROLE_DOCTOR') {
                $days = array('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY');
                foreach ($days as $day) {
                    $schedule = new Schedule();
                    $schedule->setDay($day);
                    $schedule->setStartTime(new DateTime('09:00'));
                    $schedule->setEndTime(new DateTime('16:00'));
                    $schedule->setDoctor($user);

                    $entityManager->persist($schedule);
                    $entityManager->flush();
                }
            }

            // add doctor 'consultation' service
            if ($user->getRole() === 'ROLE_DOCTOR') {
                $service = new Service();
                $service->setName('Consultation');
                $service->setDoctor($user);

                $entityManager->persist($service);
                $entityManager->flush();
            }

            // send verification email
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@talkspace.fr', 'TalkSpace'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('emails/verify_email.html.twig')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('auth/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_dashboard');
    }
}
