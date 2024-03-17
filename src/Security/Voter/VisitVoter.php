<?php

namespace App\Security\Voter;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Visit;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VisitVoter extends Voter
{
    public const MANAGE = 'VISIT_MANAGE';
    public const VIEW = 'VISIT_VIEW';
    public const LIST_ALL = 'VISIT_LIST_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if (
            in_array($attribute, [self::MANAGE, self::VIEW])
            && $subject instanceof Visit
        ) {
            return true;
        }

        return false;
    }


    /** @param ?Visit $subject */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (
            !$user instanceof Patient &&
            !$user instanceof Doctor &&
            !$user instanceof Admin
        ) {
            return false;
        }

        // list all

        if ($attribute === self::LIST_ALL) {
            return true;
        }

        // view and manage

        if (!$subject instanceof Visit) {
            return false;
        }

        if ($attribute === self::VIEW) {
            return $user instanceof Admin ||
                $subject->getPatient() === $user ||
                $subject->getDoctor() === $user;
        }

        if ($attribute === self::MANAGE) {
            return $user instanceof Doctor && $subject->getDoctor() === $user;
        }

        return false;
    }
}
