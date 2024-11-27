<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/api/albums', name: 'api_get_albums', methods: ['GET'])]
    public function getAllAlbums(AlbumRepository $albumRepository): JsonResponse
    {
        $albums = $albumRepository->findAll();
        return $this->json($albums, Response::HTTP_OK);
    }

    #[Route('/api/albums/{id}', name: 'api_get_album', methods: ['GET'])]
    public function getAlbum(int $id, AlbumRepository $albumRepository): JsonResponse
    {
        $album = $albumRepository->find($id);

        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($album, Response::HTTP_OK);
    }

    #[Route('/api/albums', name: 'api_create_album', methods: ['POST'])]
    public function createAlbum(
        Request $request,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['date'], $data['artist_id'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $artist = $artistRepository->find($data['artist_id']);
        if (!$artist) {
            return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
        }

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setDate(new \DateTime($data['date']));
        $album->setArtist($artist);

        $entityManager->persist($album);
        $entityManager->flush();

        return $this->json($album, Response::HTTP_CREATED);
    }

    #[Route('/api/albums/{id}', name: 'api_update_album', methods: ['PUT'])]
    public function updateAlbum(
        int $id,
        Request $request,
        AlbumRepository $albumRepository,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $album = $albumRepository->find($id);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['title'])) {
            $album->setTitle($data['title']);
        }

        if (isset($data['date'])) {
            $album->setDate(new \DateTime($data['date']));
        }

        if (isset($data['artist_id'])) {
            $artist = $artistRepository->find($data['artist_id']);
            if (!$artist) {
                return $this->json(['message' => 'Artist not found'], Response::HTTP_NOT_FOUND);
            }
            $album->setArtist($artist);
        }

        $entityManager->flush();

        return $this->json($album, Response::HTTP_OK);
    }

    #[Route('/api/albums/{id}', name: 'api_delete_album', methods: ['DELETE'])]
    public function deleteAlbum(
        int $id,
        AlbumRepository $albumRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $album = $albumRepository->find($id);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($album);
        $entityManager->flush();

        return $this->json(['message' => 'Album deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/albums/{id}/songs', name: 'api_get_album_songs', methods: ['GET'])]
    public function getAlbumSongs(int $id, AlbumRepository $albumRepository): JsonResponse
    {
        $album = $albumRepository->find($id);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $songs = $album->getSongs();
        return $this->json($songs, Response::HTTP_OK);
    }

    #[Route('/api/albums/{id}/songs', name: 'api_add_song_to_album', methods: ['POST'])]
    public function addSongToAlbum(
        int $id,
        Request $request,
        AlbumRepository $albumRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $album = $albumRepository->find($id);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['length'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $song = new Song();
        $song->setTitle($data['title']);
        $song->setLength((int)$data['length']);
        $song->setAlbum($album);

        $entityManager->persist($song);
        $entityManager->flush();

        return $this->json($song, Response::HTTP_CREATED);
    }
}