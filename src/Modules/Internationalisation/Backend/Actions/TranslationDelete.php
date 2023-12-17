<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\DeleteTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete translations
 */
final class TranslationDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        $translation = $this->getEntityFromRequestOrNull($request, Translation::class, 'action.id');

        return $this->handleDeleteForm(
            $request,
            DeleteTranslation::class,
            TranslationIndex::getActionSlug(),
            successFlashMessage: FlashMessage::success(
                'EntityDeleted',
                ['%entity%' => $this->translator->trans($translation?->getTranslatable())]
            ),
        );
    }
}
