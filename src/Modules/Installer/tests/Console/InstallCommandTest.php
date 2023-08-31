<?php

namespace ForkCMS\Modules\Installer\Tests\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;

class InstallCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel(['environment' => 'test_install']);
        $application = new Application($kernel);
        $command = $application->find('doctrine:schema:drop');
        $command->run(
            new ArrayInput(
                [
                    '--full-database' => true,
                    '--force' => true,
                ]
            ),
            new BufferedOutput(),
        );
        $command = $application->find('forkcms:installer:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful('Are you sure the test database is empty?');

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Fork CMS is installed', $output);
    }
}
