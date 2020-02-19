<?php

namespace Common\BlockEditor\Blocks;

final class LinkButtonBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'shortcut' => 'CMD+SHIFT+B',
            'class' => 'BlockEditor.blocks.SingleButton',
        ];
    }

    public function getValidation(): array
    {
        return [
            'text' => [
                'type' => 'string',
                'required' => true,
            ],
            'url' => [
                'type' => 'string',
                'required' => true,
            ],
            'type' => [
                'type' => 'string',
                'canBeOnly' => [
                    'default',
                    'primary',
                    'info',
                    'warning',
                    'danger',
                ],
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/LinkButtonBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return '/js/vendors/editor.js';
    }
}
