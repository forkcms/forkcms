<?php

namespace ForkCMS\Modules\Blog\Domain\Article\Command;

use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\Status;

class CreateArticle extends ArticleDataTransferObject
{
    public function __construct()
    {
        parent::__construct();

        $this->status = Status::DRAFT;
    }

    public function setEntity(Article $blogPost): void
    {
        $this->blogPostEntity = $blogPost;
    }
}
