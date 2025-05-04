<?php

namespace App\Controller\Api;

use App\DTO\User\CreateUserDTO;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface  $entityManager,
        private UserService             $userService,
        private SerializerInterface     $serializer,
    )
    {
    }

    #[Route('/api/register', name: 'registerUser', methods: ['POST'])]
    public function register(
        #[ValueResolver('dto')] CreateUserDto $createUserDTO,
    ): JsonResponse
    {
        $user = $this->userService->store($this->entityManager, $createUserDTO);
        $userData = $this->serializer->normalize(
            data: $user,
            context: [
                '_format' => 'json',
                'groups' => ['profile-view']
            ]
        );

        return new JsonResponse([
            'message' => 'User was created successfully',
            'user' => $userData
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/profile', name: 'userProfile', methods: ['GET'])]
    public function show(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (! $user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $userData = $this->serializer->normalize(
            data: $user,
            context: [
                '_format' => 'json',
                'groups' => ['profile-view']
            ]
        );

        return new JsonResponse(
            $userData,
            Response::HTTP_OK
        );
    }
}