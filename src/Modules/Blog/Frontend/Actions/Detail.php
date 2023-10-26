<?php

namespace ForkCMS\Modules\Blog\Frontend\Actions;

use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use ForkCMS\Modules\Frontend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Frontend\Domain\Block\BlockServices;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Detail extends AbstractActionController
{
    private ?Article $article = null;

    public function __construct(
        BlockServices $blockServices,
        private readonly ArticleRepository $articleRepository,
    ) {
        parent::__construct($blockServices);
    }

    protected function execute(Request $request, Response $response): void
    {
        $this->getData($request);

        if ($this->article === null) {
            throw new NotFoundHttpException();
        }

        // TODO add OpenGraph data?

        $this->assign('article', $this->article);
    }

    private function getData(Request $request): void
    {
        if ($request->get('revision_id') !== null) {
            $this->article = $this->articleRepository->getFullBlogPostByRevisionId(
                $request->get('revision_id'),
                Locale::current()
            );

            return;
        }

        if ($request->get('slug') !== null) {
            $this->article = $this->articleRepository->getFullBlogPostBySlug(
                $request->get('slug'),
                Locale::current()
            );

            return;
        }

        throw new NotFoundHttpException();
    }
}
