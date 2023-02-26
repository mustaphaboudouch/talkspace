<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActivateVoter extends Voter
{
    public const ACTIVE = 'USER_ACTIVE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ACTIVE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::ACTIVE:
                return $subject->isActive() === true;
                break;
        }

        return false;
    }
}
