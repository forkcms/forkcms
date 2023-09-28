<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use ForkCMS\Core\Domain\Kernel\Kernel;
use ForkCMS\Core\Domain\PDO\ForkConnection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

$loader = require __DIR__ . '/../../../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

(new Dotenv())->loadEnv(__DIR__ . '/../../../.env', null, 'test', []);

function installTest()
{
    $kernel = new Kernel('test_install', true);
    $kernel->boot();

    $application = new Application($kernel);
    $application->setAutoExit(false);

    $application->run(
        new ArrayInput([
            'command' => 'cache:clear',
            '--no-warmup' => '1',
            '--env' => 'test_install',
        ])
    );
    $application->run(
        new ArrayInput([
            'command' => 'cache:clear',
            '--no-warmup' => '1',
            '--env' => 'test',
        ])
    );

    fwrite(STDERR, print_r('Create test database', true));
    $application->run(
        new ArrayInput([
            'command' => 'doctrine:database:drop',
            '--if-exists' => '1',
            '--force' => '1',
        ])
    );

    $application->run(
        new ArrayInput([
            'command' => 'doctrine:database:create',
        ])
    );

    $application->run(
        new ArrayInput([
            'command' => 'forkcms:installer:install',
        ])
    );

    $kernel->shutdown();
}

if ((($_ENV['TEST_DATABASE'] ?? 'cached') === 'fresh') || ForkConnection::get('test_instal')->exec('select 1') === false) {
    installTest();
}

return $loader;
