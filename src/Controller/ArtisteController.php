<?php

namespace App\Controller;

use App\Entity\Artiste;
use App\Entity\Album;
use App\Repository\ArtisteRepository;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtisteController extends AbstractController
{
    #[Route('/artistes', name: 'api_get_artistes', methods: ['GET'])]
       public function getAllArtists(ArtisteRepository $artisteRepository): JsonResponse
       {
           $artistes = $artisteRepository->findAll();
           return $this->json($artistes, Response::HTTP_OK);
       }

       #[Route('/artistes/{id}', name: 'api_get_artist', methods: ['GET'])]
       public function getArtiste(int $id, ArtisteRepository $artisteRepository): JsonResponse
       {
           $artiste = $artisteRepository->find($id);

           if (!$artiste) {
               return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
           }

           return $this->json($artiste, Response::HTTP_OK);
       }

       #[Route('/artistes', name: 'api_create_artiste', methods: ['POST'])]
       public function createArtiste(Request $request, EntityManagerInterface $entityManager): JsonResponse
       {
           $data = json_decode($request->getContent(), true);

           // Vérification des données nécessaires
           if (!isset($data['name'], $data['style'])) {
               return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
           }

           // Création d'un nouvel artiste
           $artiste = new Artiste();
           $artiste->setName($data['name']);
           $artiste->setStyle($data['style']);

           // Sauvegarde de l'artiste dans la base de données
           $entityManager->persist($artiste);
           $entityManager->flush();

           // Retour de la réponse JSON avec l'artiste créé
           return $this->json($artiste, Response::HTTP_CREATED);
       }

       #[Route('/artistes/{id}', name: 'api_update_artiste', methods: ['PUT', 'PATCH'])]
       public function updateArtiste(int $id, Request $request, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager): JsonResponse
       {
           $data = json_decode($request->getContent(), true);

           $artiste = $artisteRepository->find($id);
           if (!$artiste) {
               return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
           }

           if (isset($data['name'])) {
               $artiste->setName($data['name']);
           }

           if (isset($data['style'])) {
               $artiste->setStyle($data['style']);
           }

           $entityManager->flush();

           return $this->json($artiste, Response::HTTP_OK);
       }

       #[Route('/artistes/{id}', name: 'api_delete_artiste', methods: ['DELETE'])]
       public function deleteArtist(int $id, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager): JsonResponse
       {
           $artiste = $artisteRepository->find($id);
           if (!$artiste) {
               return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
           }

           $entityManager->remove($artiste);
           $entityManager->flush();

           return $this->json(['message' => 'Artiste deleted successfully'], Response::HTTP_NO_CONTENT);
       }

    #[Route('/artistes/{id}/albums', name: 'api_get_artist_albums', methods: ['GET'])]
    public function getArtistAlbums(int $id, ArtisteRepository $artisteRepository): JsonResponse
    {
        $artiste = $artisteRepository->find($id);
        if (!$artiste) {
            return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
        }

        $albums = $artiste->getAlbums();
        return $this->json($albums, Response::HTTP_OK);
    }

    #[Route('/artistes/{id}/albums', name: 'api_add_album_to_artiste', methods: ['POST'])]
    public function addAlbumToArtiste(
        int $id,
        Request $request,
        ArtisteRepository $artisteRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $artiste = $artisteRepository->find($id);
        if (!$artiste) {
            return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['date'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setDate(new \DateTime($data['date']));
        $album->setArtiste($artiste);

        $entityManager->persist($album);
        $entityManager->flush();

        return $this->json($album, Response::HTTP_CREATED);
    }
}