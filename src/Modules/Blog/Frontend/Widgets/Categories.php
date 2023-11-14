<?php

namespace ForkCMS\Modules\Blog\Frontend\Widgets;

use ForkCMS\Modules\Blog\Domain\Category\CategoryRepository;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Frontend\Domain\Widget\AbstractWidgetController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Categories extends AbstractWidgetController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly CategoryRepository $categoryRepository,
    ) {
        parent::__construct($blockServices);
    }


    protected function execute(Request $request, Response $response): void
    {
        $categories = $this->categoryRepository->getAllCategories($this->translator->getLocale());

        $this->assign('categories', $categories);
    }
}
