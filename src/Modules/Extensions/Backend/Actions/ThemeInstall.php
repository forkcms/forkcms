<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Theme\Command\InstallTheme;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use InvalidArgumentException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Install a theme.
 */
final class ThemeInstall extends AbstractFormActionController
{
    public function __construct(ActionServices $services, private readonly ThemeRepository $themeRepository)
    {
        parent::__construct($services);
    }

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
                $theme = $this->themeRepository->findInstallable()[$form->getData()['name']]
                    ?? throw new InvalidArgumentException('Theme not found');

                $this->commandBus->dispatch(new InstallTheme($theme));

                return new RedirectResponse(ThemeIndex::getActionSlug()->generateRoute($this->router));
            },
            successFlashMessageCallback: function (FormInterface $form): FlashMessage {
                return FlashMessage::success('ThemeInstalled', ['%theme%' => $form->getData()['name']]);
            }
        );
    }
}
