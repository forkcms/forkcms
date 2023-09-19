<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

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
        return $this->parseWithTwig('@BlockEditor/Blocks/RawBlock.html.twig', $data);
    }
}
