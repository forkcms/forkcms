<?php

namespace Common\BlockEditor\Blocks;

final class ParagraphBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Paragraph',
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
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/ParagraphBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return '/js/vendors/editor.js';
    }
}
