<?php

namespace ForkCMS\Modules\Blog\Frontend\Actions;

use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use ForkCMS\Modules\Blog\Domain\Category\CategoryRepository;
use ForkCMS\Modules\Frontend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Category extends AbstractActionController
{
    public function __construct(
        BlockServices $blockServices,
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository
    ) {
        parent::__construct($blockServices);
    }

    protected function execute(Request $request, Response $response): void
    {
        dump($this->categoryRepository->getAllCategories($this->translator->getLocale()));
        die;

        if ($request->get('slug') === null) {
            throw new NotFoundHttpException();
        }

        $category = $this->categoryRepository->getCategoryBySlug($request->get('slug'), Locale::current());
        if ($category === null) {
            throw new NotFoundHttpException();
        }

        $articles = $this->articleRepository->getPaginatedForCategory($category, Locale::current());

        $this->assign('category', $category);
        $this->assign('items', $articles);
    }
}
