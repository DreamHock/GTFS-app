<?php

namespace App\Security\Voter;

use App\Document\User;
use App\Service\PermissionEngine;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    private PermissionEngine $permissionEngine;

    public function __construct(PermissionEngine $permissionEngine)
    {
        $this->permissionEngine = $permissionEngine;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // We assume all attributes could be permissions
        // The engine will check if the permission exists
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // If the user is anonymous, deny access
        if (!$user instanceof User) {
            return false;
        }

        // Use the permission engine to check if the user has permission
        return $this->permissionEngine->can($user, $attribute, $subject);
    }
}