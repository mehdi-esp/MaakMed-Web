<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Pharmacy;
use App\Entity\User;

class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const MANAGE = 'USER_MANAGE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            in_array($attribute, [self::VIEW, self::MANAGE])
            && $subject instanceof User
        ) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (
            !$user instanceof Patient &&
            !$user instanceof Doctor &&
            !$user instanceof Pharmacy &&
            !$user instanceof Admin
        ) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Admin can view all users, others can only view themselves
                return $user instanceof Admin || $user === $subject;
            case self::MANAGE:
                // All users can manage (edit) their own accounts
                return $user === $subject;
        }

        return false;
    }
}
