<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{


    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $entityManager, private readonly UserRepository $userRepo)
    {
    }

    #[Route('', methods: ["POST"])]
    public function index(#[MapRequestPayload] CreateUserDto $dto): JsonResponse
    {

        $found = $this->userRepo->findOneBy(['username' => $dto->username]);

        if($found){
            throw new ConflictHttpException('User with given username already exists');
        }

        $user = new User();
        $user->setUsername($dto->username);
        $hashedPassword = $this->passwordHasher->hashPassword($user,$dto->plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => "Account Created"], Response::HTTP_CREATED);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('',methods: ["GET"])]
    public function get(#[CurrentUser] User $user){
        return $this->json([
            'id'=>$user->getId(),
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles()
        ]);
    }
}
