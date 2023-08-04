<?php

namespace ForkCMS\Modules\Backend\Domain\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class SuperAdminVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'ROLE_MODULE_WIDGET__')
               || str_starts_with($attribute, 'ROLE_MODULE_ACTION__')
               || str_starts_with($attribute, 'ROLE_MODULE_AJAX_ACTION__')
               || str_starts_with($attribute, 'ROLE_MODULE__');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $user->isSuperAdmin() && $user->hasAccessToBackend();
    }
}
