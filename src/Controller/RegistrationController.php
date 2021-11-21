<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[AsController]
class RegistrationController extends AbstractController
{


    public function __construct(private UserPasswordHasherInterface $passwordHasher, private JWTTokenManagerInterface $tokenManager, private Security $security, private EntityManagerInterface $em)
    {
    }

    public function __invoke(User $data, Request $request): User
    {
        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPassword()));

        return $data;
    }
}
