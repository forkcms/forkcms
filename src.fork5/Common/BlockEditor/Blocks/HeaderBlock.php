<?php

namespace Common\BlockEditor\Blocks;

final class HeaderBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'shortcut' => 'CMD+SHIFT+H',
            'class' => 'BlockEditor.blocks.Header',
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
            'level' => [
                'type' => 'int',
                'canBeOnly' => [1, 2, 3, 4, 5, 6],
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/HeaderBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
