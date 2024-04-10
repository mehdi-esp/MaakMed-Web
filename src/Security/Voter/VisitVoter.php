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
    public const CONSULT_USER_INFO = 'CONSULT_USER_INFO';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if ($subject instanceof Patient || $subject instanceof Doctor) {
            return $attribute === self::CONSULT_USER_INFO;
        }

        if (
            in_array($attribute, [self::MANAGE, self::VIEW])
            && $subject instanceof Visit
        ) {
            return true;
        }

        return false;
    }


    /** @param ?Visit|Patient|Doctor $subject */
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

        // user info

        if ($attribute === self::CONSULT_USER_INFO) {
            if (!($subject instanceof Patient || $subject instanceof Doctor)) {
                throw new \LogicException('Unreachable');
            }

            if ($user instanceof Doctor) {
                return $subject instanceof Patient;
            }

            if ($user instanceof Patient) {
                return $subject instanceof Doctor;
            }

            // Admin can consult all

            return true;
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
