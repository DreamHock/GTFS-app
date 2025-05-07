<?php

namespace App\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security, Request $request, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager): JsonResponse
    {

        if (!$security->getUser()) {
            return new JsonResponse([
                'message' => 'You are not logged in',
            ], 401);
        }

        $security->logout(false);
        return new JsonResponse([
            'message' => 'Logged out successfully',
        ]);
    }

    #[Route('/api/login_check', name: 'api_login_check')]
    public function api_login() {}
}
