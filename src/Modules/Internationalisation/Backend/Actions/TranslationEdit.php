<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Command\ChangeTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationType;
use Symfony\Component\Form\FormInterface;
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

        $this->header->addBreadcrumb(new Breadcrumb($translation->getTranslatable()));

        $this->addDeleteForm(['id' => $translation->getId()], TranslationDelete::getActionSlug());

        return $this->handleForm(
            request: $request,
            formType: TranslationType::class,
            formData: new ChangeTranslation($translation),
            redirectResponse: new RedirectResponse(TranslationIndex::getActionSlug()->generateRoute($this->router)),
            successFlashMessageCallback: static fn (FormInterface $form) => FlashMessage::success(
                'EntityEdited',
                ['entity' => $this->translator->trans($form->getData()->getEntity()->getTranslatable())]
            ),
        );
    }
}
