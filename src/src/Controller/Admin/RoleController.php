<?php

namespace App\Controller\Admin;

use App\Document\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/roles')]
class RoleController extends AbstractController
{
    #[Route('/', name: 'admin_roles_index', methods: ['GET'])]
    public function index(DocumentManager $dm): JsonResponse
    {
        $roles = $dm->getRepository(Role::class)->findAll();
        
        return $this->json($roles);
    }

    // #[Route('/new', name: 'admin_roles_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, DocumentManager $dm): Response
    // {
    //     // Implementation for creating a new role
    //     // ...
    // }

    // #[Route('/{id}/edit', name: 'admin_roles_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Role $role, DocumentManager $dm): Response
    // {
    //     // Implementation for editing a role
    //     // ...
    // }

    // #[Route('/{id}/permissions', name: 'admin_roles_permissions', methods: ['GET', 'POST'])]
    // public function managePermissions(Request $request, Role $role, DocumentManager $dm): Response
    // {
    //     // Implementation for managing role permissions
    //     // ...
    // }
}