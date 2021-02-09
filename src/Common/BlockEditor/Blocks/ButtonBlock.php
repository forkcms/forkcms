<?php

namespace Common\BlockEditor\Blocks;

final class ButtonBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Button',
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
            'style' => [
                'type' => 'string',
                'canBeOnly' => ['primary', 'secondary'],
            ],
            'targetBlank' => [
                'type' => 'bool',
                'required' => false,
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/Button.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
