<?php
use Codeception\Util\Stub;

use ForkCMS\Bundle\InstallerBundle\Entity\InstallationData as InstallationData;

class InstallationDataTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testInstallationDataProperties()
    {
        $dbPort = 3306;
        $dbDatabase = 'fork_cms';
        $dbHostname = 'localhost';
        $dbPassword = '$ecure';
        $dbUsername = 'root';
        $debugEmail = 'developers@forkcms.org';
        $defaultInterfaceLanguage = 'en';
        $interfaceLanguages = array('en', 'fr');
        $email = 'admin@forkcms.org';
        $password = 'adminpwd';
        $languageType = 'multiple';
        $modules = array('Tags', 'Modules', 'Profiles', 'Users');

        $installationData = new InstallationData();
        $installationData->setDbDatabase($dbDatabase);
        $installationData->setDbPort($dbPort);
        $installationData->setDbHostname($dbHostname);
        $installationData->setDbPassword($dbPassword);
        $installationData->setDbUsername($dbUsername);
        $installationData->setDebugEmail($debugEmail);
        $installationData->setDefaultInterfaceLanguage($defaultInterfaceLanguage);
        $installationData->setDifferentDebugEmail(true);
        $installationData->setEmail($email);
        $installationData->setPassword($password);
        $installationData->setDefaultLanguage($defaultInterfaceLanguage);
        $installationData->setExampleData(true);
        $installationData->setLanguageType($languageType);
        $installationData->setInterfaceLanguages($interfaceLanguages);
        $installationData->setLanguages($interfaceLanguages);
        $installationData->setModules($modules);
        $installationData->setSameInterfaceLanguage(true);

        $this->assertEquals($dbDatabase, $installationData->getDbDatabase());
        $this->assertEquals($dbPort, $installationData->getDbPort());
        $this->assertEquals($dbHostname, $installationData->getDbHostname());
        $this->assertEquals($dbPassword, $installationData->getDbPassword());
        $this->assertEquals($dbUsername, $installationData->getDbUsername());
        $this->assertEquals($debugEmail, $installationData->getDebugEmail());
        $this->assertEquals($defaultInterfaceLanguage, $installationData->getDefaultInterfaceLanguage());
        $this->assertEquals(true, $installationData->hasDifferentDebugEmail());
        $this->assertEquals($email, $installationData->getEmail());
        $this->assertEquals($password, $installationData->getPassword());
        $this->assertEquals($defaultInterfaceLanguage, $installationData->getDefaultLanguage());
        $this->assertEquals(true, $installationData->hasExampleData());
        $this->assertEquals($languageType, $installationData->getLanguageType());
        $this->assertEquals($interfaceLanguages, $installationData->getInterfaceLanguages());
        $this->assertEquals($defaultInterfaceLanguage, $installationData->getInterfaceLanguage());
        $this->assertEquals($interfaceLanguages, $installationData->getLanguages());
        $this->assertEquals($modules, $installationData->getModules());
        $this->assertEquals(true, $installationData->getSameInterfaceLanguage());
    }
}