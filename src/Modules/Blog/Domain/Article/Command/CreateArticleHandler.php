<?php

namespace ForkCMS\Modules\Blog\Domain\Article\Command;

use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\ArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateArticleHandler
{
    public function __construct(
        private readonly ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(CreateArticle $createArticle): void
    {
        $createArticle->id = $this->articleRepository->getNextIdForLanguage($createArticle->locale);

        $article = Article::fromDataTransferObject($createArticle);
        // dd($article);
        $this->articleRepository->save($article);
    }
}
