<?php

namespace ForkCMS\Modules\Installer\Controller;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Requirement\RequirementsChecker;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RequirementCheckerController extends AbstractStepController
{
    public function __construct(
        Environment $twig,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        MessageBusInterface $commandBus,
        private RequirementsChecker $requirementsChecker,
        private string $rootDir,
    ) {
        parent::__construct(
            $twig,
            $router,
            $formFactory,
            $commandBus,
        );
    }

    public function __invoke(Request $request): Response
    {
        $step = InstallerStep::REQUIREMENTS;

        // if all our requirements are met, go to the next step
        if ($this->requirementsChecker->passes($request->query->getBoolean('ignoreWarnings'))) {
            $installerConfiguration = InstallerConfiguration::fromCache();
            $installerConfiguration->withRequirementsStep();
            InstallerConfiguration::toCache($installerConfiguration);

            return new RedirectResponse($this->router->generate($step->next()->route()));
        }

        return new Response(
            $this->twig->render(
                $step->template(),
                [
                    'checker' => $this->requirementsChecker,
                    'rootDir' => realpath($this->rootDir),
                ]
            )
        );
    }
}
