<?php

namespace App\Security\Voter;

use App\Entity\Admin;
use App\Entity\Issue;
use App\Entity\IssueResponse;
use App\Entity\Patient;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IssueResponseVoter extends Voter
{
public const VIEW = 'ISSUE_RESPONSE_VIEW';
public const EDIT = 'ISSUE_RESPONSE_EDIT';
public const DELETE = 'ISSUE_RESPONSE_DELETE';

protected function supports(string $attribute, mixed $subject): bool
{
if (in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]) && $subject instanceof IssueResponse) {
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

if (!$subject instanceof IssueResponse) {
return false;
}

// Admin can view, edit, and delete all responses
if ($user instanceof Admin) {
return true;
}

// Patient can view responses related to their issues
if ($attribute === self::VIEW) {
return $subject->getIssue()->getUser() === $user;
}

return false;
}
}
