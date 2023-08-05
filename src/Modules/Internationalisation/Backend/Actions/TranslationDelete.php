<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\DeleteTranslation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete translations
 */
final class TranslationDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        return $this->handleDeleteForm(
            $request,
            DeleteTranslation::class,
            TranslationIndex::getActionSlug()
        );
    }
}
