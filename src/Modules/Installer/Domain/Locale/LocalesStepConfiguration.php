<?php

namespace ForkCMS\Modules\Installer\Domain\Locale;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStepConfiguration;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class LocalesStepConfiguration implements InstallerStepConfiguration
{
    /**
     * The type of locale setup: single or multiple.
     */
    public bool $multilingual = false;

    /**
     * Do we use the same locales for the backend or not?
     */
    public bool $sameInterfaceLocale = true;

    /**
     * The locales to install Fork in.
     *
     * @var Locale[]
     * @Assert\Count(min=1)
     */
    public array $locales = [];

    /**
     * The backend interface locales to install for Fork.
     *
     * @var Locale[]
     * @Assert\Count(min=1)
     */
    public array $userLocales = [];

    /**
     * The default locale for this Fork installation.
     *
     * @Assert\NotBlank()
     */
    public ?Locale $defaultLocale = null;

    /**
     * The default locale for the Fork backend.
     *
     * @Assert\NotBlank()
     */
    public ?Locale $defaultUserLocale = null;

    /**
     * @param Locale[] $locales
     * @param Locale[] $userLocales
     */
    private function __construct(
        bool $multilingual = false,
        array $locales = [],
        array $userLocales = [],
        ?Locale $defaultLocale = null,
        ?Locale $defaultUserLocale = null,
    ) {
        $this->multilingual = $multilingual;
        $this->setLocales(...$locales);
        $this->setUserLocales(...$userLocales);
        $this->sameInterfaceLocale = $this->locales === $this->userLocales;
        $this->defaultLocale = $defaultLocale;
        $this->defaultUserLocale = $defaultUserLocale;
    }

    public static function fromInstallerConfiguration(InstallerConfiguration $installerConfiguration): static
    {
        if (!$installerConfiguration->hasStep(self::getStep())) {
            return new self();
        }

        return new self(
            $installerConfiguration->isMultilingual(),
            $installerConfiguration->getLocales(),
            $installerConfiguration->getUserLocales(),
            $installerConfiguration->getDefaultLocale(),
            $installerConfiguration->getDefaultUserLocale(),
        );
    }

    public static function fromArray(array $configuration): static
    {
        return new self(
            $configuration['multilingual'],
            $configuration['locales'],
            $configuration['interface-locales'],
            $configuration['default-locale'],
            $configuration['default-interface-locale']
        );
    }

    public function normalise(): void
    {
        if (!$this->multilingual) {
            $this->locales = [$this->defaultLocale];
        }

        if ($this->sameInterfaceLocale) {
            $this->userLocales = $this->locales;
        }
    }

    private function setLocales(Locale ...$locales): void
    {
        $this->locales = $locales;
    }

    private function setUserLocales(Locale ...$userLocales): void
    {
        $this->userLocales = $userLocales;
    }

    public static function getStep(): InstallerStep
    {
        return InstallerStep::locales;
    }
}
