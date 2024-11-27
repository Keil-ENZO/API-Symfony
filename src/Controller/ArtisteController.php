<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Album;
use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtisteController extends AbstractController
{
    #[Route('/api/artistes', name: 'api_get_artistes', methods: ['GET'])]
       public function getAllArtists(ArtisteRepository $artisteRepository): JsonResponse
       {
           $artists = $artisteRepository->findAll();
           return $this->json($artists, Response::HTTP_OK);
       }

       #[Route('/api/artistes/{id}', name: 'api_get_artist', methods: ['GET'])]
       public function getArtist(int $id, ArtisteRepository $artisteRepository): JsonResponse
       {
           $artist = $artisteRepository->find($id);

           if (!$artist) {
               return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
           }

           return $this->json($artist, Response::HTTP_OK);
       }

       #[Route('/api/artistes', name: 'api_create_artist', methods: ['POST'])]
       public function createArtist(Request $request, EntityManagerInterface $entityManager): JsonResponse
       {
           $data = json_decode($request->getContent(), true);

           // Vérification des données nécessaires
           if (!isset($data['name'], $data['style'])) {
               return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
           }

           // Création d'un nouvel artiste
           $artist = new Artiste();
           $artist->setName($data['name']);
           $artist->setStyle($data['style']);

           // Sauvegarde de l'artiste dans la base de données
           $entityManager->persist($artist);
           $entityManager->flush();

           // Retour de la réponse JSON avec l'artiste créé
           return $this->json($artist, Response::HTTP_CREATED);
       }

       #[Route('/api/artistes/{id}', name: 'api_update_artist', methods: ['PUT'])]
       public function updateArtist(int $id, Request $request, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager): JsonResponse
       {
           $data = json_decode($request->getContent(), true);

           $artist = $artisteRepository->find($id);
           if (!$artist) {
               return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
           }

           if (isset($data['name'])) {
               $artist->setName($data['name']);
           }

           if (isset($data['style'])) {
               $artist->setStyle($data['style']);
           }

           $entityManager->flush();

           return $this->json($artist, Response::HTTP_OK);
       }

       #[Route('/api/artistes/{id}', name: 'api_delete_artist', methods: ['DELETE'])]
       public function deleteArtist(int $id, ArtisteRepository $artisteRepository, EntityManagerInterface $entityManager): JsonResponse
       {
           $artist = $artisteRepository->find($id);
           if (!$artist) {
               return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
           }

           $entityManager->remove($artist);
           $entityManager->flush();

           return $this->json(['message' => 'Artist deleted successfully'], Response::HTTP_NO_CONTENT);
       }

    #[Route('/api/artistes/{id}/albums', name: 'api_get_artist_albums', methods: ['GET'])]
    public function getArtistAlbums(int $id, ArtistRepository $artistRepository): JsonResponse
    {
        $artist = $artistRepository->find($id);
        if (!$artist) {
            return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
        }

        $albums = $artist->getAlbums();
        return $this->json($albums, Response::HTTP_OK);
    }

    #[Route('/api/artistes/{id}/albums', name: 'api_add_album_to_artist', methods: ['POST'])]
    public function addAlbumToArtist(
        int $id,
        Request $request,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $artist = $artistRepository->find($id);
        if (!$artist) {
            return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['date'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setDate(new \DateTime($data['date']));
        $album->setArtist($artist);

        $entityManager->persist($album);
        $entityManager->flush();

        return $this->json($album, Response::HTTP_CREATED);
    }
}