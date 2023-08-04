<?php

namespace ForkCMS\Modules\Installer\Controller;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Installer\InstallForkCMS;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class InstallController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly RouterInterface $router,
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $step = InstallerStep::install;
        $installerConfiguration = InstallerConfiguration::fromCache();

        if (!$installerConfiguration->isValidForStep($step)) {
            return new RedirectResponse($this->router->generate($step->previous()->route()));
        }

        $this->commandBus->dispatch(new InstallForkCMS($installerConfiguration));

        return new Response(
            $this->twig->render(
                $step->template(),
                [
                    'data' => $installerConfiguration,
                ]
            )
        );
    }
}
