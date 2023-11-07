<?php

namespace ForkCMS\Modules\BlockEditor\Domain\Blocks;

use ForkCMS\Core\Domain\Header\Asset\Asset;

final class TableBlock extends AbstractBlock
{
    public function getConfig(): array
    {
        return [
            'class' => 'BlockEditor.blocks.Table',
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
        ];
    }

    public function parse(array $data): string
    {
        return $this->parseWithTwig('@BlockEditor/Blocks/TableBlock.html.twig', $data);
    }
}
