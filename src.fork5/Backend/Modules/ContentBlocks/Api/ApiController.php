<?php

namespace Backend\Modules\ContentBlocks\Api;

use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Status;
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
     * @Rest\Get("/content-blocks/{language}")
     */
    public function getContentblocksAction(string $language): JsonResponse
    {
        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize(
                    $this->blockRepository->findBy(['locale' => $language, 'status' => Status::active()]),
                    'json'
                ),
                true
            )
        );
    }

    /**
     * @Rest\Get("/content-blocks/{language}/{id}")
     */
    public function getContentblockAction(string $language, int $id): JsonResponse
    {
        $contentBlock = $this->blockRepository->findOneBy(
            [
                'locale' => $language,
                'id' => $id,
                'status' => Status::active(),
            ]
        );

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
