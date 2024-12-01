<?php

namespace App\Controller;

use App\Entity\Chanson;
use App\Entity\Album;
use App\Repository\SongRepository;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChansonController extends AbstractController
{
    #[Route('/chansons', name: 'api_get_chansons', methods: ['GET'])]
    public function getAllChansons(SongRepository $songRepository): JsonResponse
    {
        $chansons = $songRepository->findAll();
        return $this->json($chansons, Response::HTTP_OK);
    }

    #[Route('/chansons/{id}', name: 'api_get_chanson', methods: ['GET'])]
    public function getSong(int $id, SongRepository $songRepository): JsonResponse
    {
        $song = $songRepository->find($id);

        if (!$song) {
            return $this->json(['message' => 'Song not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($song, Response::HTTP_OK);
    }

    #[Route('/chansons', name: 'api_create_chanson', methods: ['POST'])]
    public function createSong(
        Request $request,
        AlbumRepository $albumRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['length'], $data['album_id'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $album = $albumRepository->find($data['album_id']);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $song = new Song();
        $song->setTitle($data['title']);
        $song->setLength($data['length']);
        $song->setAlbum($album);

        $entityManager->persist($song);
        $entityManager->flush();

        return $this->json($song, Response::HTTP_CREATED);
    }

    #[Route('/chansons/{id}', name: 'api_update_chanson', methods: ['PUT', 'PATCH'])]
    public function updateSong(
        int $id,
        Request $request,
        SongRepository $songRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $song = $songRepository->find($id);
        if (!$song) {
            return $this->json(['message' => 'Song not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['title'])) {
            $song->setTitle($data['title']);
        }

        if (isset($data['length'])) {
            $song->setLength($data['length']);
        }

        $entityManager->flush();

        return $this->json($song, Response::HTTP_OK);
    }

    #[Route('/chansons/{id}', name: 'api_delete_chanson', methods: ['DELETE'])]
    public function deleteSong(
        int $id,
        SongRepository $songRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $song = $songRepository->find($id);
        if (!$song) {
            return $this->json(['message' => 'Song not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($song);
        $entityManager->flush();

        return $this->json(['message' => 'Song deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/chansons/album/{albumId}', name: 'api_get_Chansons_by_album', methods: ['GET'])]
    public function getChansonsByAlbum(int $albumId, AlbumRepository $albumRepository): JsonResponse
    {
        $album = $albumRepository->find($albumId);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $chansons = $album->getChansons();
        return $this->json($chansons, Response::HTTP_OK);
    }
}