<?php

require_once __DIR__.'/../../autoload.php';
require_once __DIR__.'/../Cache/RemoveCache.php';
require_once __DIR__.'/../Core/PrepareForReinstall.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Console\Cache\RemoveCache;
use Console\Core\PrepareForReinstall;

class PrepareForReinstallCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new RemoveCache);
        $application->add(new PrepareForReinstall);

        $command = $application->find('core:prepare_for_reinstall');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName())
        );

        // check the output
        $this->assertRegExp('/Ready for reinstall/', $commandTester->getDisplay());

        // create some instances
        $fs = new Filesystem();

        // build path to the rootdirectory
        $rootPath = __DIR__ . '/../..';

        // check if configuration files are removed
        $this->assertFalse($fs->exists($rootPath . '/app/config/parameters.yml'));

        // check if installed.txt is removed
        $this->assertFalse($fs->exists($rootPath . '/install/cache/installed.txt'));
    }
}
