<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artiste;
use App\Entity\Chanson;
use App\Repository\AlbumRepository;
use App\Repository\ArtisteRepository;
use App\Repository\ChansonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    #[Route('/albums', name: 'api_get_albums', methods: ['GET'])]
    public function getAllAlbums(AlbumRepository $albumRepository): JsonResponse
    {
        $albums = $albumRepository->findAll();
        return $this->json($albums, Response::HTTP_OK);
    }

    #[Route('/albums/{id}', name: 'api_get_album', methods: ['GET'])]
    public function getAlbum(int $id, AlbumRepository $albumRepository): JsonResponse
    {
        $album = $albumRepository->find($id);

        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($album, Response::HTTP_OK);
    }

    #[Route('/albums', name: 'api_create_album', methods: ['POST'])]
    public function createAlbum(
        Request $request,
        ArtisteRepository $artisteRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['date'], $data['artiste_id'])) {
            return $this->json(['message' => 'Missing required data'], Response::HTTP_BAD_REQUEST);
        }

        $artiste = $artisteRepository->find($data['artiste_id']);
        if (!$artiste) {
            return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
        }

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setDate(new \DateTime($data['date']));
        $album->setArtiste($artiste);

        $entityManager->persist($album);
        $entityManager->flush();

        return $this->json($album, Response::HTTP_CREATED);
    }

    #[Route('/albums/{id}', name: 'api_update_album',methods: ['PUT', 'PATCH'])]
    public function updateAlbum(
        int $id,
        Request $request,
        AlbumRepository $albumRepository,
        ArtisteRepository $artisteRepository,
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

        if (isset($data['artiste_id'])) {
            $artiste = $artisteRepository->find($data['artiste_id']);
            if (!$artiste) {
                return $this->json(['message' => 'Artiste not found'], Response::HTTP_NOT_FOUND);
            }
            $album->setArtiste($artiste);
        }

        $entityManager->flush();

        return $this->json($album, Response::HTTP_OK);
    }

    #[Route('/albums/{id}', name: 'api_delete_album', methods: ['DELETE'])]
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

    #[Route('/albums/{id}/chansons', name: 'api_get_album_chansons', methods: ['GET'])]
    public function getAlbumChansons(int $id, AlbumRepository $albumRepository): JsonResponse
    {
        $album = $albumRepository->find($id);
        if (!$album) {
            return $this->json(['message' => 'Album not found'], Response::HTTP_NOT_FOUND);
        }

        $chansons = $album->getChansons();
        return $this->json($chansons, Response::HTTP_OK);
    }

    #[Route('/albums/{id}/chansons', name: 'api_add_Chanson_to_album', methods: ['POST'])]
    public function addChansonToAlbum(
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

        $chanson = new Chanson();
        $chanson->setTitle($data['title']);
        $chanson->setLength((int)$data['length']);
        $chanson->setAlbum($album);

        $entityManager->persist($chanson);
        $entityManager->flush();

        return $this->json($chanson, Response::HTTP_CREATED);
    }
}