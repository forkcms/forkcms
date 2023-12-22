<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\CreateTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add a translation
 */
final class TranslationAdd extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $translation = $request->attributes->get('slug') === null
            ? null : $this->getEntityFromRequest($request, Translation::class);

        return $this->handleForm(
            request: $request,
            formType: TranslationType::class,
            formData: new CreateTranslation($translation),
            redirectResponse: new RedirectResponse(TranslationIndex::getActionSlug()->generateRoute($this->router)),
            successFlashMessageCallback: fn (FormInterface $form) => FlashMessage::success(
                'EntityAdded',
                ['entity' => $this->translator->trans($form->getData()->getEntity()->getTranslatable())]
            ),
        );
    }
}
