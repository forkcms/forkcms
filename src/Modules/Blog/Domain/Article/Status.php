<?php

namespace ForkCMS\Modules\Blog\Domain\Article;

enum Status: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
