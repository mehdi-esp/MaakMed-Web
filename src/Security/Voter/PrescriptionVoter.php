<?php

namespace App\Security\Voter;

use App\Entity\Prescription;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Admin;
use App\Entity\Pharmacy;
use App\Entity\Visit  ; 
use App\Entity\Patient;

class PrescriptionVoter extends Voter
{
    public const VIEW = 'INVOICE_VIEW';
    public const MANAGE = 'INVOICE_MANAGE';
    public const LIST_ALL = 'INVOICE_LIST_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if (
            in_array($attribute, [self::VIEW, self::MANAGE])
            && $subject instanceof Prescription
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
            !$user instanceof Pharmacy &&
            !$user instanceof Admin
        ) {
            return false;
        }

        // list all
        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if (!$subject instanceof Prescription) {
            return false;
        }

        if ($attribute === self::VIEW) {
            return $user instanceof Admin || $subject->getVisit()->getPatient() === $user || $subject->getVisit()->getDoctor() === $user;
        }
        if ($attribute === self::MANAGE) {
            return $subject->getVisit()->getDoctor() === $user;
        }

        return false;
    }
}
