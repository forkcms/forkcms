<?php

namespace ForkCMS\Modules\Extensions\Backend\Actions;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInformation;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleRepository;
use Pageon\DoctrineDataGridBundle\Column\Column;
use Symfony\Component\HttpFoundation\Request;

/**
 * Overview of installed and installable modules.
 */
final class ModuleIndex extends AbstractActionController
{
    public function __construct(
        ActionServices $actionServices,
        private readonly ModuleInstallerLocator $moduleInstallerLocator,
        private readonly ModuleRepository $moduleRepository,
    ) {
        parent::__construct($actionServices);
    }

    protected function execute(Request $request): void
    {
        $installedModules = $this->moduleRepository->findAllIndexed();

        $installed = [];
        $notInstalled = [];
        foreach ($this->moduleInstallerLocator->getModuleInstallersForOverview() as $installer) {
            if (array_key_exists($installer::getModuleName()->getName(), $installedModules)) {
                $installed[$installer::getModuleName()->getName()] = $installer->getInformation();
            } else {
                $notInstalled[$installer::getModuleName()->getName()] = $installer->getInformation();
            }
        }
        $this->assign(
            'installedModules',
            $this->dataGridFactory->forArray(ModuleInformation::class, $installed)
        );
        if ($this->isAllowed(ModuleInstall::getActionSlug())) {
            $this->assign(
                'notInstalledModules',
                $this->dataGridFactory->forArray(
                    ModuleInformation::class,
                    $notInstalled,
                    null,
                    [],
                    null,
                    new Column(
                        name: 'moduleName',
                        label: 'lbl.Install',
                        valueCallback: [$this, 'getInstallButton'],
                        html: true,
                        showColumnLabel: false,
                    )
                ),
            );
        }
    }

    public function getInstallButton(string $moduleName): string
    {
        return $this->twig->render('@Extensions/Backend/Forms/ModuleInstall.html.twig', [
            'moduleName' => $moduleName,
            'installForm' => $this->formFactory->create(
                ActionType::class,
                [
                    'id' => $moduleName,
                ],
                [
                    'actionSlug' => ModuleInstall::getActionSlug(),
                ]
            )->createView(),
        ]);
    }
}
