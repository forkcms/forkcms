<?php

namespace ForkCMS\Modules\Blog\Domain\Article\Command;

use ForkCMS\Modules\Blog\Domain\Article\Article;

class EditArticle extends ArticleDataTransferObject
{
    public function __construct(Article $article)
    {
        parent::__construct($article);
    }
}
