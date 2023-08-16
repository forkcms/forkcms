<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\DeleteContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete an existing ContentBlock.
 */
final class ContentBlockDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        return $this->handleDeleteForm(
            $request,
            DeleteContentBlock::class,
            ContentBlockIndex::getActionSlug()
        );
    }
}
