<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\ChangeContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit an existing ContentBlock.
 */
final class ContentBlockEdit extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        /** @var ContentBlock $contentBlock */
        $contentBlock = $this->getEntityFromRequest($request, ContentBlock::class);

        if ($this->getRepository(ContentBlock::class)->count([]) > 1) {
            $this->addDeleteForm(
                ['id' => $contentBlock->getId()],
                ActionSlug::fromFQCN(ContentBlockDelete::class)
            );
        }

        return $this->handleForm(
            request: $request,
            formType: ContentBlockType::class,
            formData: new ChangeContentBlock($contentBlock),
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(ContentBlockIndex::getActionSlug()->generateRoute($this->router)),
        );
    }
}
