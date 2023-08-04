<?php

namespace Common\BlockEditor\Blocks;

final class ListBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.List',
        ];
    }

    public function getValidation(): array
    {
        return [
            'style' => [
                'type' => 'string',
                'canBeOnly' => ['ordered', 'unordered'],
            ],
            'items' => [
                'type' => 'array',
                'data' => [
                    '-' => [
                        'type' => 'string',
                        'allowedTags' => 'i,b,u',
                    ],
                ],
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/ListBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
