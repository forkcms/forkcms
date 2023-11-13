<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

final class TextImageBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.TextImageBlock',
            'inlineToolbar' => true,
            'tunes' => ['alignmentBlockTune']
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
            'alignment' => [
                'type' => 'string',
                'canBeOnly' => ['left', 'center', 'right'],
            ],
            'imagePosition' => [
                'type' => 'string',
                'canBeOnly' => ['left', 'right'],
            ],
        ];
    }

    public function parse(array $data, array $tunes = []): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/TextImageBlock.html.twig', $data, $tunes);
    }
}
