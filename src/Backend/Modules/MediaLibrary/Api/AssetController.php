<?php

namespace Backend\Modules\MediaLibrary\Api;

use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
