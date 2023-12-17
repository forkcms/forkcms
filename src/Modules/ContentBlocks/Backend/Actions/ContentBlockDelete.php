<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\DeleteContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete an existing content block.
 */
final class ContentBlockDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        $contentBlock = $this->getEntityFromRequestOrNull($request, ContentBlock::class, 'action.id');

        return $this->handleDeleteForm(
            $request,
            DeleteContentBlock::class,
            ContentBlockIndex::getActionSlug(),
            successFlashMessage: FlashMessage::success('Deleted', ['%contentBlock%' => $contentBlock?->getTitle()]),
        );
    }
}
