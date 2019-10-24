<?php

namespace Common\BlockEditor\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ParseBlocksExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'parse_blocks',
                [$this, 'parseBlocks'],
                ['needs_environment' => false, 'needs_context' => false, 'is_safe' => ['all']]
            ),
        ];
    }

    public function parseBlocks(string $json): string
    {
        $data = json_decode($json, true);

        if ($data === false) {
            return $json;
        }

        return '<h1>content</h1>';
    }
}
