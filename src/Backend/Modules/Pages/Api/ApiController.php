<?php

namespace Backend\Modules\Pages\Api;

use Backend\Modules\Pages\Domain\Page\PageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiController
{
    /** @var PageRepository */
    private $pageRepository;

    public function __construct(
        PageRepository $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @Rest\Get("/pages/{language}/{id}")
     */
    public function getPageAction(string $language, int $id): JsonResponse
    {
        $latest = $this->pageRepository->getLatestForApi($id, $language);

        if ($latest === null) {
            throw new NotFoundHttpException();
        }

        return JsonResponse::create($latest);
    }
}
