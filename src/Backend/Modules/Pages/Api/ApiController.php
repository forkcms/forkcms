<?php

namespace Backend\Modules\Pages\Api;

use Backend\Modules\Pages\Domain\Page\PageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiController
{
    /** @var PageRepository */
    private $pageRepository;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(PageRepository $pageRepository, SerializerInterface $serializer)
    {
        $this->pageRepository = $pageRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/pages/{language}/{id}")
     */
    public function getPageAction(string $language, int $id): JsonResponse
    {
        $latest = $this->pageRepository->getLatest($id, $language);

        if ($language === null) {
            throw new NotFoundHttpException();
        }

        return JsonResponse::create(
            json_decode(
                $this->serializer->serialize(
                    $latest,
                    'json'
                ),
                true
            )
        );
    }
}
