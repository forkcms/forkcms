<?php

namespace ForkCMS\Modules\Blog\Frontend\Actions;

use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use ForkCMS\Modules\Frontend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Index extends AbstractActionController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly ArticleRepository $articleRepository
    ) {
        parent::__construct($blockServices);
    }

    protected function execute(Request $request, Response $response): void
    {
        $requestedPage = $request->get('page') ?? 1;
        if (!is_numeric($requestedPage) || $requestedPage < 1) {
            $requestedPage = 1;
        }

        $articles = $this->articleRepository->getAllPaginated($this->translator->getLocale(), $requestedPage);

        $this->assign('items', $articles);
        $this->assign('url', $request->getRequestUri());
    }
}
