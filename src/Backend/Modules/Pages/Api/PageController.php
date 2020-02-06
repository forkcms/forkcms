<?php

namespace Backend\Modules\Pages\Api;

use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Frontend\Core\Engine\Breadcrumb;
use Frontend\Core\Engine\Footer;
use Frontend\Core\Engine\Navigation;
use Frontend\Core\Engine\Url;
use Frontend\Core\Header\Header;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

final class PageController
{
    /** @var PageRepository */
    private $pageRepository;

    /** @var PageBlockRepository */
    private $pageBlockRepository;

    /** @var KernelInterface */
    private $kernel;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        PageRepository $pageRepository,
        PageBlockRepository $pageBlockRepository,
        KernelInterface $kernel,
        RequestStack $requestStack
    ) {
        $this->pageRepository = $pageRepository;
        $this->pageBlockRepository = $pageBlockRepository;
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
    }

    /**
     * @Rest\Get("/pages/{locale}/{id}")
     */
    public function getPageAction(string $locale, int $id): JsonResponse
    {
        $latest = $this->pageRepository->getLatestForApi($id, Locale::fromString($locale));

        if ($latest === null) {
            throw new NotFoundHttpException();
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

    /**
     * @Rest\Get("/pages/{locale}/{pageId}/page-block/{pageBlockId}")
     */
    public function getPageBlockAction(string $locale, int $pageId, int $pageBlockId): JsonResponse
    {
        $locale = Locale::fromString($locale);
        $latest = $this->pageRepository->getLatestForApi($pageId, $locale);

        if ($latest === null) {
            return JsonResponse::create(null, JsonResponse::HTTP_NOT_FOUND);
        }

        $pageBlock = $latest['extras'][$pageBlockId] ?? [];

        if (empty($pageBlock) || $pageBlock['hidden']) {
            return JsonResponse::create(null, JsonResponse::HTTP_NOT_FOUND);
        }


        $mockRequest = Request::create(rtrim(Navigation::getUrl($latest['id'], $locale), '/'));
        $this->requestStack->push($mockRequest);

        // we need this hurray
        new Url($this->kernel);
        new Header($this->kernel);
        new Breadcrumb($this->kernel);
        new Footer($this->kernel);

        $moduleExtra = ModuleExtra::createModuleExtra(
            $this->kernel,
            new ModuleExtraType($pageBlock['type']),
            $pageBlock['module'],
            $pageBlock['action'],
            $pageBlock['data']
        );
        $moduleExtra->execute();
        $extraVariables = $moduleExtra->getTemplate()->getAssignedVariables();

        $this->requestStack->pop();

        return JsonResponse::create($extraVariables);
    }
}
