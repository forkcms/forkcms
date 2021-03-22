<?php

namespace Common\BlockEditor\Blocks;

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
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/QuoteBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
