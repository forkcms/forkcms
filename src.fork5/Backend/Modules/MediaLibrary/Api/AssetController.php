<?php

namespace Backend\Modules\MediaLibrary\Api;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

final class AssetController
{
    /** @var MediaItemRepository */
    private $mediaItemRepository;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(MediaItemRepository $mediaItemRepository, SerializerInterface $serializer)
    {
        $this->mediaItemRepository = $mediaItemRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/medialibrary/asset/{uuid}")
     */
    public function getAssetAction(string $uuid): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($this->mediaItemRepository->find($uuid), 'json')
        );
    }

    /**
     * @Rest\Get("/medialibrary/asset/{uuid}/direct")
     */
    public function getAssetContentAction(string $uuid): Response
    {
        $mediaItem = $this->mediaItemRepository->find($uuid);

        if (!$mediaItem instanceof MediaItem) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($mediaItem->getAbsolutePath());
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        if (FileinfoMimeTypeGuesser::isSupported()) {
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess($mediaItem->getAbsolutePath()));
        } else {
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($mediaItem->getAbsolutePath())
        );

        return $response;
    }

    /**
     * @Rest\Get("/medialibrary/asset/{uuid}/download")
     */
    public function downloadAssetAction(string $uuid): Response
    {
        $mediaItem = $this->mediaItemRepository->find($uuid);

        if (!$mediaItem instanceof MediaItem) {
            throw new NotFoundHttpException();
        }

        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($mediaItem->getAbsolutePath()));
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . basename($mediaItem->getAbsolutePath()) . '";'
        );
        $response->headers->set('Content-length', filesize($mediaItem->getAbsolutePath()));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($mediaItem->getAbsolutePath()));

        return $response;
    }
}
