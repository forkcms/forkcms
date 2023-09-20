<?php

namespace ForkCMS\Modules\Blog\Domain\BlogPost;

enum Status: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
