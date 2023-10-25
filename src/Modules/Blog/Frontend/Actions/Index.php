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
        $articles = $this->articleRepository->getAllPaginated($this->translator->getLocale());

        $this->assign('items', $articles);
    }
}
