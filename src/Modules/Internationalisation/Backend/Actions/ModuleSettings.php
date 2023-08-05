<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\ModuleSettings\Command\ChangeModuleSettings;
use ForkCMS\Modules\Internationalisation\Domain\ModuleSettings\ModuleSettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ModuleSettings extends AbstractFormActionController
{
    protected function addBreadcrumbForRequest(Request $request): void
    {
        // no action specific breadcrumb needed
    }

    protected function getFormResponse(Request $request): ?Response
    {
        return $this->handleSettingsForm(
            $request,
            ModuleSettingsType::class,
            new ChangeModuleSettings(...$this->getRepository(InstalledLocale::class)->findAll()),
        );
    }
}
