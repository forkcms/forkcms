<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

enum Status: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
