<?php

namespace Backend\Modules\ContentBlocks\Api;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiController
{
    /** @var ContentBlockRepository */
    private $blockRepository;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(ContentBlockRepository $blockRepository, SerializerInterface $serializer)
    {
        $this->blockRepository = $blockRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/content-blocks")
     */
    public function getContentblocksAction(): JsonResponse
    {
        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize($this->blockRepository->findAll(), 'json'),
                true
            )
        );
    }

    /**
     * @Rest\Get("/content-blocks/{language}/{id}")
     */
    public function getContentblockAction(string $language, int $id): JsonResponse
    {
        $contentBlock = $this->blockRepository->findOneBy(['locale' => $language, 'id' => $id]);

        if (!$contentBlock instanceof ContentBlock) {
            throw new NotFoundHttpException();
        }

        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize($contentBlock, 'json'),
                true
            )
        );
    }
}
