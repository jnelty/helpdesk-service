<?php

namespace App\ArgumentResolver\Api;

use App\DTO\User\CreateUserDTO;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserService $userService,
    )
    {
    }

    #[Route('/api/register', name: 'registerUser', methods: ['POST'])]
    public function register(
        #[ValueResolver('dto')] CreateUserDto $createUserDTO,
    ): JsonResponse
    {
        $user = $this->userService->store($this->entityManager, $createUserDTO);

        return new JsonResponse([
            'message' => 'User was created successfully'
        ], Response::HTTP_CREATED);
    }
}