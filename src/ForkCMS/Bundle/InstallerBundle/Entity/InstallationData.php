<?php

namespace ForkCMS\Bundle\InstallerBundle\Entity;

/**
 * This object contains all fork data
 *
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class InstallationData
{
	/**
	 * The host of the database
	 *
	 * @var string
	 */
	protected $dbHostname;

	/**
	 * The username for the database
	 *
	 * @var string
	 */
	protected $dbUsername;

	/**
	 * The password for the database
	 *
	 * @var string
	 */
	protected $dbPassword;

	/**
	 * The database name
	 *
	 * @var string
	 */
	protected $dbDatabase;

	/**
	 * The port for the database
	 *
	 * @var int
	 */
	protected $dbPort = 3306;

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
	protected $modules;

	/**
	 * do we install exampleData?
	 *
	 * @var bool
	 */
	protected $exampleData;

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
	 */
	protected $email;

	/**
	 * The backend password for the GOD user
	 *
	 * @var string
	 */
	protected $password;

    /**
     * Gets the The host of the database.
     *
     * @return string
     */
    public function getDbHostname()
    {
        return $this->dbHostname;
    }

    /**
     * Sets the The host of the database.
     *
     * @param string $dbHostname the db hostname
     * @return self
     */
    protected function setDbHostname($dbHostname)
    {
        $this->dbHostname = $dbHostname;

        return $this;
    }

    /**
     * Gets the The username for the database.
     *
     * @return string
     */
    public function getDbUsername()
    {
        return $this->dbUsername;
    }

    /**
     * Sets the The username for the database.
     *
     * @param string $dbUsername the db username
     * @return self
     */
    protected function setDbUsername($dbUsername)
    {
        $this->dbUsername = $dbUsername;

        return $this;
    }

    /**
     * Gets the The password for the database.
     *
     * @return string
     */
    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    /**
     * Sets the The password for the database.
     *
     * @param string $dbPassword the db password
     * @return self
     */
    protected function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;

        return $this;
    }

    /**
     * Gets the The database name.
     *
     * @return string
     */
    public function getDbDatabase()
    {
        return $this->dbDatabase;
    }

    /**
     * Sets the The database name.
     *
     * @param string $dbDatabase the db database
     * @return self
     */
    protected function setDbDatabase($dbDatabase)
    {
        $this->dbDatabase = $dbDatabase;

        return $this;
    }

    /**
     * Gets the The port for the database.
     *
     * @return int
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * Sets the The port for the database.
     *
     * @param int $dbPort the db port
     * @return self
     */
    protected function setDbPort($dbPort)
    {
        $this->dbPort = $dbPort;

        return $this;
    }

    /**
     * Gets the The languages to install Fork in.
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Sets the The languages to install Fork in.
     *
     * @param array $languages the languages
     * @return self
     */
    protected function setLanguages(array $languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Gets the The backend interface languages to install for Fork.
     *
     * @return array
     */
    public function getInterfaceLanguages()
    {
        return $this->interfaceLanguages;
    }

    /**
     * Sets the The backend interface languages to install for Fork.
     *
     * @param array $interfaceLanguages the interface languages
     * @return self
     */
    protected function setInterfaceLanguages(array $interfaceLanguages)
    {
        $this->interfaceLanguages = $interfaceLanguages;

        return $this;
    }

    /**
     * Gets the The default language for this Fork installation.
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Sets the The default language for this Fork installation.
     *
     * @param string $defaultLanguage the default language
     * @return self
     */
    protected function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Gets the The default language for the Fork backend.
     *
     * @return string
     */
    public function getDefaultInterfaceLanguage()
    {
        return $this->defaultInterfaceLanguage;
    }

    /**
     * Sets the The default language for the Fork backend.
     *
     * @param string $defaultInterfaceLanguage the default interface language
     * @return self
     */
    protected function setDefaultInterfaceLanguage($defaultInterfaceLanguage)
    {
        $this->defaultInterfaceLanguage = $defaultInterfaceLanguage;

        return $this;
    }

    /**
     * Gets the The modules to install Fork in.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Sets the The modules to install Fork in.
     *
     * @param array $modules the modules
     * @return self
     */
    protected function setModules(array $modules)
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Gets the do we install exampleData?.
     *
     * @return bool
     */
    public function getExampleData()
    {
        return $this->exampleData;
    }

    /**
     * Sets the do we install exampleData?.
     *
     * @param bool $exampleData the example data
     * @return self
     */
    protected function setExampleData($exampleData)
    {
        $this->exampleData = $exampleData;

        return $this;
    }

    /**
     * Gets the Do we use a different debug emailaddress.
     *
     * @return bool
     */
    public function getDifferentDebugEmail()
    {
        return $this->differentDebugEmail;
    }

    /**
     * Sets the Do we use a different debug emailaddress.
     *
     * @param bool $differentDebugEmail the different debug email
     * @return self
     */
    protected function setDifferentDebugEmail($differentDebugEmail)
    {
        $this->differentDebugEmail = $differentDebugEmail;

        return $this;
    }

    /**
     * Gets the The custom debug emailaddress, if applicable.
     *
     * @return string
     */
    public function getDebugEmail()
    {
        return $this->debugEmail;
    }

    /**
     * Sets the The custom debug emailaddress, if applicable.
     *
     * @param string $debugEmail the debug email
     * @return self
     */
    protected function setDebugEmail($debugEmail)
    {
        $this->debugEmail = $debugEmail;

        return $this;
    }

    /**
     * Gets the The backend login email for the GOD user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the The backend login email for the GOD user.
     *
     * @param string $email the email
     * @return self
     */
    protected function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the The backend password for the GOD user.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the The backend password for the GOD user.
     *
     * @param string $password the password
     * @return self
     */
    protected function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
