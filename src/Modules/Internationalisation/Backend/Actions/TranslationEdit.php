<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\ChangeTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit a translation
 */
final class TranslationEdit extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $translation = $this->getEntityFromRequest($request, Translation::class);

        $this->header->addBreadcrumb(new Breadcrumb($translation->getKey()->getName()));

        $this->addDeleteForm(['id' => $translation->getId()], TranslationDelete::getActionSlug());

        return $this->handleForm(
            request: $request,
            formType: TranslationType::class,
            formData: new ChangeTranslation($translation),
            flashMessage: FlashMessage::success('Edited'),
            redirectResponse: new RedirectResponse(TranslationIndex::getActionSlug()->generateRoute($this->router))
        );
    }
}
