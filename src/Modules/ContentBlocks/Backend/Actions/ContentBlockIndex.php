<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContentBlock overview.
 */
final class ContentBlockIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $this->renderDataGrid(ContentBlock::class);
    }
}
