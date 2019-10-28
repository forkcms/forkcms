<?php

namespace Common\BlockEditor\Blocks;

final class ParagraphBlock extends EditorBlock
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
        return '<p>' . $data['text'] . '</p>';
    }

    public function getJavaScriptUrl(): ?string
    {
        return '/js/editor.js';
    }
}
