<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController
{
    public function login(): JsonResponse
    {
        // LexikJWTAuthenticationBundle interceptera cette route pour gérer l'authentification
        return new JsonResponse(['message' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}