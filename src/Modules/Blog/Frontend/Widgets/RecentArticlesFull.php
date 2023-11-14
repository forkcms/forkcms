<?php

namespace ForkCMS\Modules\Blog\Frontend\Widgets;

use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Blog\Domain\Category\CategoryRepository;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Frontend\Domain\Widget\AbstractWidgetController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecentArticlesFull extends AbstractWidgetController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly ArticleRepository $articleRepository,
        private readonly ModuleSettings $moduleSettings
    ) {
        parent::__construct($blockServices);
    }


    protected function execute(Request $request, Response $response): void
    {
        $limit = $this->moduleSettings->get(
            ModuleName::fromString('Blog'),
            'recent_articles_full_num_items',
            5
        );

        $this->assign('articles', $this->articleRepository->getAll($this->translator->getLocale(), $limit));
    }
}
