<?php

namespace ForkCMS\Modules\Installer\Controller;

use ForkCMS\Modules\Installer\Domain\Locale\LocalesStepConfiguration;
use ForkCMS\Modules\Installer\Domain\Locale\LocalesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LocalesController extends AbstractStepController
{
    public function __invoke(Request $request): Response
    {
        return $this->handleInstallationStep(
            LocalesType::class,
            LocalesStepConfiguration::class,
            $request
        );
    }
}
