<?php

namespace App\Security\Voter;

use App\Entity\Admin;
use App\Entity\Issue;
use App\Entity\Patient;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IssueVoter extends Voter
{
    public const VIEW = 'ISSUE_VIEW';
    public const EDIT = 'ISSUE_EDIT';
    public const DELETE = 'ISSUE_DELETE';
    public const LIST_ALL = 'ISSUE_LIST_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if (in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]) && $subject instanceof Issue) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Patient && !$user instanceof Admin) {
            return false;
        }

        if ($attribute === self::LIST_ALL) {
            return true;
        }

        if (!$subject instanceof Issue) {
            return false;
        }

        if ($user instanceof Admin) {
            return true;
        }

        // patient can view, edit and delete only his issues

        if ($attribute === self::VIEW) {
            return $subject->getUser() === $user;
        }
        if ($attribute === self::EDIT) {
            return $subject->getUser() === $user;
        }
        if ($attribute === self::DELETE) {
            return $subject->getUser() === $user;
        }


        return false;
    }
}
