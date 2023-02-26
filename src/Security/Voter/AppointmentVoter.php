<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentVoter extends Voter
{
    public const EDIT = 'APPOINTMENT_EDIT';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT])
            && $subject instanceof \App\Entity\Appointment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            default:
                return $user->getId() === $subject->getDoctor()->getId() || $user->getId() === $subject->getPatient()->getId();
                break;
        }

        return false;
    }
}
