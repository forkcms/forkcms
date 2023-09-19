<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

final class QuoteBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Quote',
        ];
    }

    public function getValidation(): array
    {
        return [
            'text' => [
                'type' => 'string',
                'required' => true,
                'allowedTags' => 'b,i,a[href]',
            ],
            'author' => [
                'type' => 'string',
                'required' => true,
                'allowedTags' => 'b,i,a[href]',
            ],
            'alignment' => [
                'type' => 'string',
                'canBeOnly' => ['left', 'center'],
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/QuoteBlock.html.twig', $data);
    }
}
