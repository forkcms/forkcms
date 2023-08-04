<?php

namespace Common\BlockEditor\Blocks;

final class RawBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Raw',
        ];
    }

    public function getValidation(): array
    {
        return [
            'html' => [
                'type' => 'string',
                'required' => true,
            ],
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('Core/Layout/Templates/EditorBlocks/RawBlock.html.twig', $data);
    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
