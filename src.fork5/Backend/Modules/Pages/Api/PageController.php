<?php

namespace Backend\Modules\Pages\Api;

use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageController
{
    /** @var PageRepository */
    private $pageRepository;

    public function __construct(
        PageRepository $pageRepository
    ) {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @Rest\Get("/pages/{locale}/{id}")
     */
    public function getPageAction(string $locale, int $id): JsonResponse
    {
        $latest = $this->pageRepository->getLatestForApi($id, Locale::fromString($locale));

        if ($latest === null) {
            return JsonResponse::create(null, JsonResponse::HTTP_NOT_FOUND);
        }

        return JsonResponse::create($latest);
    }

    /**
     * @Rest\Get("/pages/{locale}/{id}/subpages")
     */
    public function getSubPagesAction(string $locale, int $id): JsonResponse
    {
        $subPages = $this->pageRepository->getSubPagesForApi($id, Locale::fromString($locale));

        return JsonResponse::create($subPages);
    }
}
