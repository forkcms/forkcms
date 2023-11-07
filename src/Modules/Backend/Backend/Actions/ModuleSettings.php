<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Core\Domain\Kernel\Command\ClearContainerCache;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\ModuleSettings\ModuleSettingsType;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ModuleSettings extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $moduleRepository = $this->getRepository(Module::class);
        $moduleName = $this->getModuleName();

        return $this->handleForm(
            request: $request,
            formType: ModuleSettingsType::class,
            formData: new ChangeModuleSettings(
                $moduleRepository->find(ModuleName::core()) ?? throw new RuntimeException('Core module not found'),
                $moduleRepository->find($moduleName) ?? throw new RuntimeException($moduleName . ' module not found'),
                []
            ),
            flashMessage: FlashMessage::success('SettingsSaved'),
            validCallback: function (FormInterface $form): Response {
                $this->commandBus->dispatch($form->getData());
                $this->commandBus->dispatch(new ClearContainerCache());

                if (!$this->moduleSettings->get(ModuleName::fromString('Backend'), '2fa_enabled', false))
                {
                    $userRepository = $this->getRepository(User::class);
                    $users = $userRepository->findAll();

                    /** @var User $user */
                    foreach ($users as $user) {
                        $user->disableTwoFactorAuthentication();
                        $userRepository->save($user);
                    }
                }

                return new RedirectResponse(self::getActionSlug()->generateRoute($this->router));
            }
        );
    }
}
