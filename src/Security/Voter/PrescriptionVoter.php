<?php

namespace App\Security\Voter;

use App\Entity\Prescription;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Admin;
use App\Entity\Pharmacy;
use App\Entity\Visit  ; 
use App\Entity\Doctor;

use App\Entity\Patient;

class PrescriptionVoter extends Voter
{
    public const VIEW = 'PRESCRIPTION_VIEW';
    public const MANAGE = 'PRESCRIPTION_MANAGE';
    public const LIST_ALL = 'PRESCRIPTION_LIST_ALL';

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
            !$user instanceof Admin &&
            !$user instanceof Doctor
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
        if ($user instanceof Doctor && $attribute === self::MANAGE) {
            return true;
        }

        return false;
    }
}
