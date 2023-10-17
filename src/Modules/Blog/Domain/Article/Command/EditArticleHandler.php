<?php

namespace ForkCMS\Modules\Blog\Domain\Article\Command;

use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditArticleHandler
{
    public function __construct(
        private readonly ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(EditArticle $editArticle): void
    {
        $article = Article::fromDataTransferObject($editArticle);
        $previousArticle = $editArticle->getEntity();
        if ($previousArticle !== null) {
            $previousArticle->archive();
        }

        // TODO max revisions

        // TODO draft stuff

        $this->articleRepository->save($article);
    }
}
