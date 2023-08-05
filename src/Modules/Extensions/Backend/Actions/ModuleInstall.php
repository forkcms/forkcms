<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Extensions\Domain\Module\Command\InstallModules;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Install a module.
 */
final class ModuleInstall extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): Response
    {
        return $this->handleForm(
            request: $request,
            formType: ActionType::class,
            formOptions: ['actionSlug' => self::getActionSlug()],
            defaultCallback: function (): RedirectResponse {
                $this->header->addFlashMessage(FlashMessage::error('NotFound'));

                return new RedirectResponse(ModuleIndex::getActionSlug()->generateRoute($this->router));
            },
            validCallback: function (FormInterface $form): RedirectResponse {
                $this->commandBus->dispatch(new InstallModules(ModuleName::fromString($form->getData()['id'])));

                return new RedirectResponse(ModuleIndex::getActionSlug()->generateRoute($this->router));
            },
            flashMessageCallback: function (FormInterface $form): FlashMessage {
                return FlashMessage::success('ModuleInstalled', ['%module%' => $form->getData()['id']]);
            }
        );
    }
}
