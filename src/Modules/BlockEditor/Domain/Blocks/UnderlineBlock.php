<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

final class UnderlineBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Underline',
        ];
    }

    public function getValidation(): array
    {
        return [
            'text' => [
                'type' => 'string',
            ],
        ];
    }

    public function parse(array $data): string
    {
        return '<u>' . $data['text'] . '</u>';
    }
}
