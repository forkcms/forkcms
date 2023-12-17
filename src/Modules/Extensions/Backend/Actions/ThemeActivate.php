<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\ActivateTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use InvalidArgumentException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Activates a theme and deactivates the current active theme.
 */
final class ThemeActivate extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): Response
    {
        return $this->handleForm(
            request: $request,
            formType: ActionType::class,
            formOptions: [
                'actionSlug' => self::getActionSlug(),
                'id_field_name' => 'name',
            ],
            defaultCallback: function (): RedirectResponse {
                $this->header->addFlashMessage(FlashMessage::error('NotFound'));

                return new RedirectResponse(ThemeIndex::getActionSlug()->generateRoute($this->router));
            },
            validCallback: function (FormInterface $form): RedirectResponse {
                $theme = $this->getRepository(Theme::class)->find($form->getData()['name'])
                    ?? throw new InvalidArgumentException('Theme not found');

                $this->commandBus->dispatch(new ActivateTheme($theme));

                return new RedirectResponse(ThemeIndex::getActionSlug()->generateRoute($this->router));
            },
            successFlashMessageCallback: function (FormInterface $form): FlashMessage {
                return FlashMessage::success('ThemeActivated', ['%theme%' => $form->getData()['name']]);
            }
        );
    }
}
