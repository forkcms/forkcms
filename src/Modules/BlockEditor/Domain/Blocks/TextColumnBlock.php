<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

final class TextColumnBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.TextColumnBlock',
            'inlineToolbar' => true,
            'tunes' => ['alignmentBlockTune']
        ];
    }

    public function getValidation(): array
    {
        return [
            'text1' => [
                'type' => 'string',
                'required' => true,
                'allowedTags' => 'b,i,a[href]',
            ],
            'text2' => [
                'type' => 'string',
                'required' => true,
                'allowedTags' => 'b,i,a[href]',
            ]
        ];
    }

    public function parse(array $data, array $tunes = []): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/TextColumnBlock.html.twig', $data, $tunes);
    }
}
