<?php

namespace App\Service;

use App\Document\Permission;
use App\Document\PermissionRule;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use JWadhams\JsonLogic;

class PermissionEngine
{
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * Check if a user has permission to perform an action on a subject
     */
    public function can(User $user, string $permissionName, $subject = null): bool
    {
        // Get user roles
        $roles = $user->getRoleObjects();
        
        // Find the permission
        $permission = $this->documentManager->getRepository(Permission::class)
            ->findOneBy(['name' => $permissionName]);

        if (!$permission) {
            return false;
        }

        // Check if any of the user's roles has this permission
        $hasPermission = false;
        foreach ($roles as $role) {
            if ($role->getPermissions()->contains($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return false;
        }

        // Get all rules for this permission
        $rules = $permission->getRules();
        
        // If there are no rules, the permission is granted
        if ($rules->isEmpty()) {
            return true;
        }

        // Check each rule
        foreach ($rules as $rule) {
            if ($this->checkRule($rule, $user, $subject)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user satisfies a specific rule
     */
    private function checkRule(PermissionRule $rule, User $user, $subject = null): bool
    {
        // Check subject type
        if ($rule->getSubjectType() && $subject && !is_a($subject, $rule->getSubjectType())) {
            return false;
        }

        // Prepare data for JSON Logic
        $data = [
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
            'subject' => $subject ? $this->objectToArray($subject) : null,
            'now' => new \DateTime(),
        ];

        // Evaluate the rule using JSON Logic
        $conditions = $rule->getConditions();
        if (empty($conditions)) {
            return true; // If no conditions, rule passes
        }

        try {
            return JsonLogic::apply($conditions, $data);
        } catch (\Exception $e) {
            // Log the error
            return false;
        }
    }

    /**
     * Convert an object to an array for JSON Logic
     */
    private function objectToArray($object)
    {
        if (is_object($object)) {
            if (method_exists($object, 'toArray')) {
                return $object->toArray();
            }
            
            // Convert public properties to array
            return get_object_vars($object);
        }
        
        return $object;
    }
}