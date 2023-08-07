<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

enum SEOFollow: string
{
    case none = 'none';
    case follow = 'follow';
    case noFollow = 'nofollow';
}
