<?php

namespace Backend\Modules\ContentBlocks\Api;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
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

    public function getContentblocksAction(): JsonResponse
    {
        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize($this->blockRepository->findAll(), 'json'),
                true
            )
        );
    }

    public function getContentblockAction($contentBlock): JsonResponse
    {
        $contentBlock = $this->blockRepository->find($contentBlock);

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
