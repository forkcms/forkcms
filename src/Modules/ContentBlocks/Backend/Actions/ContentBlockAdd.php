<?php

namespace ForkCMS\Modules\ContentBlocks\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockType;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
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
        return $this->handleForm(
            request: $request,
            formType: ContentBlockType::class,
            formData: new CreateContentBlock(Locale::from($request->getLocale())),
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(ContentBlockIndex::getActionSlug()->generateRoute($this->router))
        );
    }
}
