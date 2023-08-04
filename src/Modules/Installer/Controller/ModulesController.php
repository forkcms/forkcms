<?php

namespace ForkCMS\Modules\Installer\Controller;

use ForkCMS\Modules\Installer\Domain\Module\ModulesStepConfiguration;
use ForkCMS\Modules\Installer\Domain\Module\ModulesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ModulesController extends AbstractStepController
{
    public function __invoke(Request $request): Response
    {
        return $this->handleInstallationStep(
            ModulesType::class,
            ModulesStepConfiguration::class,
            $request
        );
    }
}
