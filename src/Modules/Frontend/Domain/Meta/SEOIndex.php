<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

enum SEOIndex: string
{
    case none = 'none';
    case index = 'index';
    case noIndex = 'noindex';
}
