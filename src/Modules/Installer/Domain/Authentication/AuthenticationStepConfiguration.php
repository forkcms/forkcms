<?php

namespace ForkCMS\Modules\Installer\Domain\Authentication;

use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStep;
use ForkCMS\Modules\Installer\Domain\Installer\InstallerStepConfiguration;
use Symfony\Component\Validator\Constraints as Assert;

final class AuthenticationStepConfiguration implements InstallerStepConfiguration
{
    /**
     * The backend login email for the GOD user.
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email;

    /**
     * The backend password for the GOD user.
     *
     * @Assert\NotBlank()
     */
    public ?string $password;

    /**
     * Do we use a different debug emailaddress.
     */
    public bool $differentDebugEmail;

    /**
     * The custom debug emailaddress, if applicable.
     *
     * @Assert\Email()
     */
    public ?string $debugEmail;

    /**
     * Save the configuration to a yaml file.
     */
    public bool $saveConfiguration;

    /**
     * Include passwords in the configuration yaml file.
     */
    public bool $saveConfigurationWithCredentials;

    public function __construct(
        ?string $email = null,
        ?string $password = null,
        bool $differentDebugEmail = false,
        ?string $debugEmail = null,
        bool $saveConfiguration = false,
        bool $saveConfigurationWithCredentials = false
    ) {
        $this->email = $email ?? $this->getDefaultEmail();
        $this->password = $password;
        $this->differentDebugEmail = $differentDebugEmail;
        $this->debugEmail = $debugEmail;
        $this->saveConfiguration = $saveConfiguration;
        $this->saveConfigurationWithCredentials = $saveConfigurationWithCredentials;
    }

    private function getDefaultEmail(): string
    {
        $host = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        if (str_starts_with($host, '127.0.0.1') || str_starts_with($host, 'localhost')) {
            return 'info@localhost';
        }

        return 'info@' . $host;
    }

    public static function fromArray(array $configuration): static
    {
        return new self(
            $configuration['admin-email'],
            $configuration['admin-password'],
            $configuration['different-debug-email'],
            $configuration['debug-email']
        );
    }

    public static function fromInstallerConfiguration(InstallerConfiguration $installerConfiguration): static
    {
        if (!$installerConfiguration->hasStep(self::getStep())) {
            return new self();
        }

        return new self(
            $installerConfiguration->getAdminEmail(),
            $installerConfiguration->getAdminPassword(),
            $installerConfiguration->hasDifferentDebugEmail(),
            $installerConfiguration->getDebugEmail(),
            $installerConfiguration->shouldSaveConfiguration(),
            $installerConfiguration->shouldSaveConfigurationWithCredentials(),
        );
    }

    public static function getStep(): InstallerStep
    {
        return InstallerStep::authentication;
    }

    public function normalise(): void
    {
        if (!$this->differentDebugEmail) {
            $this->debugEmail = null;
        }

        if (!$this->saveConfiguration) {
            $this->saveConfigurationWithCredentials = false;
        }
    }
}
