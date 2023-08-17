<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlockHandler;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add a new ContentBlock.
 */
final class ContentBlockAdd extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $createContentBlock = new CreateContentBlock();
        $createContentBlock->locale = Locale::from($this->translator->getLocale());

        return $this->handleForm(
            request: $request,
            formType: ContentBlockType::class,
            formData: $createContentBlock,
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(ContentBlockIndex::getActionSlug()->generateRoute($this->router))
        );
    }
}
