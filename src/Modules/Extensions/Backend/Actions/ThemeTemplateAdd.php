<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\Command\CreateThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add a new theme template.
 */
final class ThemeTemplateAdd extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $theme = $this->getEntityFromRequest($request, Theme::class);
        $this->assign('theme', $theme);

        return $this->handleForm(
            request: $request,
            formType: ThemeTemplateType::class,
            formData: new CreateThemeTemplate($theme),
            redirectResponse: new RedirectResponse(
                ThemeTemplateIndex::getActionSlug()->generateRoute($this->router, ['slug' => $theme->getName()])
            ),
            flashMessageCallback: static fn (FormInterface $form) => FlashMessage::success(
                'ThemeTemplateAdded',
                ['%1$s' => $form->getData()->name]
            ),
        );
    }
}
