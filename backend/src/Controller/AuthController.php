<?php

namespace App\Controller;

use App\Dto\LoginRequest;
use App\Dto\RegisterRequest;
use App\Entity\ConsentLog;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ConsentLogger;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class AuthController extends ApiController
{
    #[Route('/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true) ?: [];
        $dto = LoginRequest::fromPayload($payload);
        if ($error = $this->validationErrorResponse($validator->validate($dto))) {
            return $error;
        }

        $email = strtolower(trim($dto->email));
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->jsonError('Utilisateur introuvable avec cet email.', 404);
        }

        if ($user->isAnonymized()) {
            return $this->jsonError('Ce compte a ete anonymise et ne peut plus se connecter.', 403);
        }

        if (!$passwordHasher->isPasswordValid($user, $dto->password)) {
            return $this->jsonError('Mot de passe incorrect.', 401);
        }

        return $this->json([
            'token' => $jwtManager->create($user),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ConsentLogger $consentLogger,
        ValidatorInterface $validator,
        #[Autowire(param: 'app.organizer_invite_code')]
        string $organizerInviteCode,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true) ?: [];
        $dto = RegisterRequest::fromPayload($payload);
        if ($error = $this->validationErrorResponse($validator->validate($dto))) {
            return $error;
        }

        if ($userRepository->findOneBy(['email' => strtolower(trim($dto->email))])) {
            return $this->jsonError('Email already used.', 409);
        }

        $roles = $dto->roles;
        if (in_array('ROLE_ORGANIZER', $roles, true) && $dto->organizerInviteCode !== $organizerInviteCode) {
            return $this->jsonError('Code organisateur invalide.', 403);
        }

        $user = (new User())
            ->setEmail($dto->email)
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setPhone($dto->phone)
            ->setConsentDate(new \DateTimeImmutable())
            ->setConsentVersion($dto->consentVersion)
            ->setRoles($roles);

        $user->setPassword($passwordHasher->hashPassword($user, $dto->password));

        $entityManager->persist($user);
        $consentLogger->log($user, ConsentLog::CONSENT_GIVEN, 'Consentement RGPD donne a l inscription.');
        $entityManager->flush();

        return $this->json(['id' => $user->getId(), 'email' => $user->getEmail()], 201);
    }
}
