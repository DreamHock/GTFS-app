<?php

namespace App\Controller\Api;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class AuthController extends AbstractController
{

    public function __construct(
        private DocumentManager $documentManager,
        private MailerInterface $mailer,
    ) {}

    #[Route('/auth/request-code', name: 'auth_request_code', methods: ['POST'])]
    public function requestCode(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        // Find or create user
        $user = $this->documentManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Generate verification code (6 digits)
        $verificationCode = random_int(100000, 999999);
        $user->setVerificationCode($verificationCode);

        // Set expiration time (15 minutes from now)
        $expiresAt = new \DateTimeImmutable('+15 minutes');
        $user->setVerificationCodeExpiredAt($expiresAt);

        $this->documentManager->persist($user);
        $this->documentManager->flush();

        // Send email with verification code
        $this->sendVerificationEmail($email, $verificationCode);

        return $this->json(['message' => 'Verification code sent to your email']);
    }

    #[Route('/auth/verify-code', name: 'auth_verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $code = $data['code'] ?? null;
        $redirectUrl = $data['redirectUrl'] ?? '/';

        if (!$email || !$code) {
            return $this->json(['error' => 'Email and code are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->documentManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $now = new \DateTimeImmutable();

        // Check if code is valid and not expired
        if ($user->getVerificationCode() != $code || $user->getVerificationCodeExpiredAt() < $now) {
            return $this->json(['error' => 'Invalid or expired verification code'], Response::HTTP_BAD_REQUEST);
        }

        // Clear verification code after successful verification
        $user->setVerificationCode(null);
        $user->setVerificationCodeExpiredAt(null);
        $this->documentManager->persist($user);
        $this->documentManager->flush();

        // Authenticate the user
        $token = $JWTTokenManager->create($user);

        return $this->json([
            'message' => 'Authentication successful',
            'token' => $token,
            'redirectUrl' => $redirectUrl
        ]);
    }

    private function sendVerificationEmail(string $email, int $code): void
    {
        $email = (new Email())
            ->from('noreply@your-app.com')
            ->to($email)
            ->subject('Your Verification Code')
            ->html("<p>Your verification code is: <strong>{$code}</strong></p><p>This code will expire in 15 minutes.</p>");

        $this->mailer->send($email);
    }
}
