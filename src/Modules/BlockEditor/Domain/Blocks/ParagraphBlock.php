<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class ParagraphBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Paragraph',
            'config' => [
                'placeholder' => TranslationKey::label('ClickHereToAddContent')
            ]
        ];
    }

    public function getValidation(): array
    {
        return [
            'text' => [
                'type' => 'string',
                'allowedTags' => 'i,b,u,a[href]',
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/ParagraphBlock.html.twig', $data);
    }
}
