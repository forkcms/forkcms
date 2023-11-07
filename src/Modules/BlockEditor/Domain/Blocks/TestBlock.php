<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

final class TestBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.TestBlock',
            'inlineToolbar' => true
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
            'imagePosition' => [
                'type' => 'string',
                'canBeOnly' => ['left', 'right'],
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/TestBlock.html.twig', $data);
    }
}
