<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    #[Route('/users', name: 'api_users', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): Response
    {
        $user = $userRepository->findAll();

        return new JsonResponse($user);
    }

}