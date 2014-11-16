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
        $email = 'admin@forkcms.org';

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

        $this->assertEquals($dbDatabase, $installationData->getDbDatabase());
        $this->assertEquals($dbPort, $installationData->getDbPort());
        $this->assertEquals($dbHostname, $installationData->getDbHostname());
        $this->assertEquals($dbPassword, $installationData->getDbPassword());
        $this->assertEquals($dbUsername, $installationData->getDbUsername());
        $this->assertEquals($debugEmail, $installationData->getDebugEmail());
        $this->assertEquals($defaultInterfaceLanguage, $installationData->getDefaultInterfaceLanguage());
        $this->assertEquals(true, $installationData->hasDifferentDebugEmail());
        $this->assertEquals($email, $installationData->getEmail());
    }
}