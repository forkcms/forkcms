<?php

namespace ForkCMS\Core\Domain\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class PhpExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('urlencode', 'urlencode'),
            new TwigFilter('rawurlencode', 'rawurlencode'),
            new TwigFilter('striptags', 'strip_tags'),
            new TwigFilter('addslashes', 'addslashes'),
            new TwigFilter('count', 'count'),
            new TwigFilter('is_array', 'is_array'),
            new TwigFilter('ucfirst', 'ucfirst'),
            new TwigFilter(
                'type',
                static fn (mixed $value): string => str_replace('Proxies\\__CG__\\', '', get_debug_type($value))
            ),
        ];
    }
}
