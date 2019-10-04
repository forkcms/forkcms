<?php

namespace Backend\Modules\ContentBlocks\Api;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function getContentBlocksAction()
    {
        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize($this->blockRepository->findAll(), 'json'),
                true
            )
        );
    }
}
