<?php

namespace ForkCMS\Modules\Blog\Domain\Comment;

enum Status: string
{
    case Moderation = 'moderation';
    case Published = 'published';
    case Spam = 'spam';
}
