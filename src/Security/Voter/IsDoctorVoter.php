<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IsDoctorVoter extends Voter
{
    public const IS_DOCTOR = 'IS_DOCTOR';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::IS_DOCTOR])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::IS_DOCTOR:
            default:
                return $subject->getRole() === 'ROLE_DOCTOR';
                break;
        }

        return false;
    }
}
