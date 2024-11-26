<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController
{
    #[Route('/api/articles', name: 'api_articles', methods: ['GET'])]
    public function getArticles(): Response
    {
        // Logique pour récupérer les articles
        return new Response('Articles');
    }
}