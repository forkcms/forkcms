<?php

namespace ForkCMS\Modules\Installer\Controller;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStepConfiguration;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

abstract class AbstractStepController
{
    public function __construct(
        protected readonly Environment $twig,
        protected readonly RouterInterface $router,
        protected readonly FormFactoryInterface $formFactory,
        protected readonly MessageBusInterface $commandBus
    ) {
    }

    abstract public function __invoke(Request $request): Response;

    /**
     * @param class-string<FormTypeInterface> $formTypeClass
     * @param class-string<InstallerStepConfiguration> $dataClass
     */
    final protected function handleInstallationStep(
        string $formTypeClass,
        string $dataClass,
        Request $request
    ): Response {
        $installerConfiguration = InstallerConfiguration::fromCache();
        $installerStepConfiguration = $this->getFormData($dataClass, $installerConfiguration);
        $step = $dataClass::getStep();
        if (!$installerConfiguration->isValidForStep($step)) {
            return new RedirectResponse($this->router->generate($step->previous()->route()));
        }

        $form = $this->formFactory->create($formTypeClass, $installerStepConfiguration);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch($form->getData());

            return new RedirectResponse($this->router->generate($step->next()->route()));
        }

        return new Response(
            $this->twig->render(
                $step->template(),
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    private function getFormData(
        string $dataClass,
        InstallerConfiguration $installerConfiguration
    ): InstallerStepConfiguration {
        /* @var $dataClass InstallerStepConfiguration */
        return $dataClass::fromInstallerConfiguration($installerConfiguration);
    }
}
