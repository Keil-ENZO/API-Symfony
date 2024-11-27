<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController
{
    public function login(): JsonResponse
    {
        return new JsonResponse(['message' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}