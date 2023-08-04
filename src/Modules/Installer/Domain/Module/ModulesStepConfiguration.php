<?php

namespace ForkCMS\Modules\Installer\Domain\Module;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstallerLocator;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStepConfiguration;
use Symfony\Component\Validator\Constraints as Assert;

final class ModulesStepConfiguration implements InstallerStepConfiguration
{
    /**
     * The modules to install Fork in.
     *
     * @var ModuleName[]
     * @Assert\Count(min=1)
     */
    public array $modules = [];

    /** do we install exampleData? */
    public bool $installExampleData;

    /** @param  ModuleName[] $modules */
    private function __construct(
        array $modules = [],
        bool $installExampleData = false
    ) {
        $this->modules = $modules;
        $this->installExampleData = $installExampleData;
    }

    public static function getStep(): InstallerStep
    {
        return InstallerStep::modules;
    }

    public static function fromArray(array $configuration): static
    {
        return new self(
            $configuration['modules'],
            $configuration['install-example-data']
        );
    }

    public static function fromInstallerConfiguration(InstallerConfiguration $installerConfiguration): static
    {
        if (!$installerConfiguration->hasStep(self::getStep())) {
            return new self();
        }

        return new self(
            $installerConfiguration->getModules(),
            $installerConfiguration->shouldInstallExampleData(),
        );
    }

    public function normalise(ModuleInstallerLocator $moduleInstallerLocator): void
    {
        $modules = array_merge($this->modules, array_values($moduleInstallerLocator->getRequiredModuleNames()));
        // if ($this->installExampleData) {
        // $modules[] = BlogInstaller::getModuleName(); @TODO reenable when adding blog
        // }

        $this->modules = array_unique($modules);
    }
}
