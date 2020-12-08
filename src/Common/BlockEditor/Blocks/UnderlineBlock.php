<?php

namespace Common\BlockEditor\Blocks;

final class UnderlineBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Underline',
        ];
    }

    public function getValidation(): array
    {
        return [
            'text' => [
                'type' => 'string',
            ],
        ];
    }

    public function parse(array $data): string
    {

    }

    public function getJavaScriptUrl(): ?string
    {
        return null;
    }
}
