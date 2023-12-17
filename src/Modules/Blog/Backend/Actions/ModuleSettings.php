<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Blog\Domain\ModuleSettings\ModuleSettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleSettings extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        return $this->handleModuleSettingsForm(
            $request,
            ModuleSettingsType::class
        );
    }
}
