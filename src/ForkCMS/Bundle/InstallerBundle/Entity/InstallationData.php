<?php

namespace ForkCMS\Bundle\InstallerBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This object contains all fork data
 */
class InstallationData
{
    /**
     * The host of the database
     *
     * @var string
     */
    protected $databaseHostname;

    /**
     * The username for the database
     *
     * @var string
     */
    protected $databaseUsername;

    /**
     * The password for the database
     *
     * @var string
     */
    protected $databasePassword;

    /**
     * The database name
     *
     * @var string
     */
    protected $databaseName;

    /**
     * The port for the database
     *
     * @var int
     */
    protected $databasePort = 3306;

    /**
     * The type of language setup: single or multiple
     *
     * @var string
     */
    protected $languageType = 'single';

    /**
     * Do we use the same languages for the backend or not?
     *
     * @var bool
     */
    protected $sameInterfaceLanguage = true;

    /**
     * The languages to install Fork in
     *
     * @var array
     */
    protected $languages;

    /**
     * The backend interface languages to install for Fork
     *
     * @var array
     */
    protected $interfaceLanguages;

    /**
     * The default language for this Fork installation
     *
     * @var string
     */
    protected $defaultLanguage;

    /**
     * The default language for the Fork backend
     *
     * @var string
     */
    protected $defaultInterfaceLanguage;

    /**
     * The modules to install Fork in
     *
     * @var array
     */
    protected $modules = [];

    /**
     * do we install exampleData?
     *
     * @var bool
     */
    protected $exampleData = true;

    /**
     * Do we use a different debug emailaddress
     *
     * @var bool
     */
    protected $differentDebugEmail;

    /**
     * The custom debug emailaddress, if applicable
     *
     * @var string
     */
    protected $debugEmail;

    /**
     * The backend login email for the GOD user
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"login"})
     * @Assert\Email(groups={"login"})
     */
    protected $email;

    /**
     * The backend password for the GOD user
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"login"})
     */
    protected $password;

    /**
     * Gets the The host of the database.
     *
     * @return string|null
     */
    public function getDatabaseHostname(): ?string
    {
        return $this->databaseHostname;
    }

    /**
     * Sets the The host of the database.
     *
     * @param string $databaseHostname the database hostname
     *
     * @return self
     */
    public function setDatabaseHostname($databaseHostname): self
    {
        $this->databaseHostname = $databaseHostname;

        return $this;
    }

    /**
     * Gets the The username for the database.
     *
     * @return string|null
     */
    public function getDatabaseUsername(): ?string
    {
        return $this->databaseUsername;
    }

    /**
     * Sets the The username for the database.
     *
     * @param string $databaseUsername the database username
     *
     * @return self
     */
    public function setDatabaseUsername($databaseUsername): self
    {
        $this->databaseUsername = $databaseUsername;

        return $this;
    }

    /**
     * Gets the The password for the database.
     *
     * @return string|null
     */
    public function getDatabasePassword(): ?string
    {
        return $this->databasePassword;
    }

    /**
     * Sets the The password for the database.
     *
     * @param string $databasePassword the database password
     *
     * @return self
     */
    public function setDatabasePassword($databasePassword): self
    {
        $this->databasePassword = $databasePassword;

        return $this;
    }

    /**
     * Gets the The database name.
     *
     * @return string|null
     */
    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }

    /**
     * Sets the The database name.
     *
     * @param string $databaseName the database name
     *
     * @return self
     */
    public function setDatabaseName($databaseName): self
    {
        $this->databaseName = $databaseName;

        return $this;
    }

    /**
     * Gets the The port for the database.
     *
     * @return int
     */
    public function getDatabasePort(): int
    {
        return $this->databasePort;
    }

    /**
     * Sets the The port for the database.
     *
     * @param int $databasePort the database port
     *
     * @return self
     */
    public function setDatabasePort($databasePort): self
    {
        $this->databasePort = $databasePort;

        return $this;
    }

    /**
     * Gets the The type of language setup: single or multiple.
     *
     * @return string|null
     */
    public function getLanguageType(): ?string
    {
        return $this->languageType;
    }

    /**
     * Sets the The type of language setup: single or multiple.
     *
     * @param string $languageType the language type
     *
     * @return self
     */
    public function setLanguageType($languageType): self
    {
        $this->languageType = $languageType;

        return $this;
    }

    /**
     * Gets the Do we use the same languages for the backend or not?.
     *
     * @return bool
     */
    public function getSameInterfaceLanguage(): bool
    {
        return $this->sameInterfaceLanguage;
    }

    /**
     * Sets the Do we use the same languages for the backend or not?.
     *
     * @param bool $sameInterfaceLanguage the same interface language
     *
     * @return self
     */
    public function setSameInterfaceLanguage($sameInterfaceLanguage): self
    {
        $this->sameInterfaceLanguage = $sameInterfaceLanguage;

        return $this;
    }

    /**
     * Helper method, only needed for the languages form
     *
     * @return string|null
     */
    public function getInterfaceLanguage(): ?string
    {
        return $this->getDefaultLanguage();
    }

    /**
     * Gets the The languages to install Fork in.
     *
     * @return array|null
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * Sets the The languages to install Fork in.
     *
     * @param array $languages the languages
     *
     * @return self
     */
    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Gets the The backend interface languages to install for Fork.
     *
     * @return array|null
     */
    public function getInterfaceLanguages(): ?array
    {
        return $this->interfaceLanguages;
    }

    /**
     * Sets the The backend interface languages to install for Fork.
     *
     * @param array $interfaceLanguages the interface languages
     *
     * @return self
     */
    public function setInterfaceLanguages(array $interfaceLanguages): self
    {
        $this->interfaceLanguages = $interfaceLanguages;

        return $this;
    }

    /**
     * Gets the The default language for this Fork installation.
     *
     * @return string|null
     */
    public function getDefaultLanguage(): ?string
    {
        return $this->defaultLanguage;
    }

    /**
     * Sets the The default language for this Fork installation.
     *
     * @param string $defaultLanguage the default language
     *
     * @return self
     */
    public function setDefaultLanguage($defaultLanguage): self
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Gets the The default language for the Fork backend.
     *
     * @return string|null
     */
    public function getDefaultInterfaceLanguage(): ?string
    {
        return $this->defaultInterfaceLanguage;
    }

    /**
     * Sets the The default language for the Fork backend.
     *
     * @param string $defaultInterfaceLanguage the default interface language
     *
     * @return self
     */
    public function setDefaultInterfaceLanguage($defaultInterfaceLanguage): self
    {
        $this->defaultInterfaceLanguage = $defaultInterfaceLanguage;

        return $this;
    }

    /**
     * Gets the The modules to install Fork in.
     *
     * @return array|null
     */
    public function getModules(): ?array
    {
        return $this->modules;
    }

    /**
     * Adds a module to the modules array
     *
     * @param string $module
     *
     * @return self
     */
    public function addModule($module): self
    {
        if (!in_array($module, $this->modules, true)) {
            $this->modules[] = $module;
        }

        return $this;
    }

    /**
     * Removes an item from the modules array
     *
     * @param string $module
     *
     * @return self
     */
    public function removeModule($module): self
    {
        $index = array_search($module, $this->modules, true);
        if ($index !== false) {
            unset($this->modules[$index]);
        }

        return $this;
    }

    /**
     * Sets the The modules to install Fork in.
     *
     * @param array $modules the modules
     *
     * @return self
     */
    public function setModules(array $modules): self
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Gets the do we install exampleData?.
     *
     * @return bool|null
     */
    public function hasExampleData(): ?bool
    {
        return $this->exampleData;
    }

    /**
     * Sets the do we install exampleData?.
     *
     * @param bool $exampleData the example data
     *
     * @return self
     */
    public function setExampleData($exampleData): self
    {
        $this->exampleData = $exampleData;

        return $this;
    }

    /**
     * Gets the Do we use a different debug emailaddress.
     *
     * @return bool|null
     */
    public function hasDifferentDebugEmail(): ?bool
    {
        return $this->differentDebugEmail;
    }

    /**
     * Sets the Do we use a different debug emailaddress.
     *
     * @param bool $differentDebugEmail the different debug email
     *
     * @return self
     */
    public function setDifferentDebugEmail($differentDebugEmail): self
    {
        $this->differentDebugEmail = $differentDebugEmail;

        return $this;
    }

    /**
     * Gets the The custom debug emailaddress, if applicable.
     *
     * @return string|null
     */
    public function getDebugEmail(): ?string
    {
        return $this->debugEmail;
    }

    /**
     * Sets the The custom debug emailaddress, if applicable.
     *
     * @param string $debugEmail the debug email
     *
     * @return self
     */
    public function setDebugEmail($debugEmail): self
    {
        $this->debugEmail = $debugEmail;

        return $this;
    }

    /**
     * Gets the The backend login email for the GOD user.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the The backend login email for the GOD user.
     *
     * @param string $email the email
     *
     * @return self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the The backend password for the GOD user.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the The backend password for the GOD user.
     *
     * @param string $password the password
     *
     * @return self
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Checks if all data needed for installation is available here
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !(
            empty($this->databaseHostname)
            || empty($this->databaseUsername)
            || empty($this->databaseName)
            || empty($this->databasePort)

            || empty($this->languages)
            || empty($this->interfaceLanguages)
            || empty($this->defaultLanguage)
            || empty($this->defaultInterfaceLanguage)

            || empty($this->modules)

            || empty($this->email)
            || empty($this->password)
        );
    }
}
