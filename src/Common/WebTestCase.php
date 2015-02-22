<?php

namespace Common;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\FileSystem\FileSystem;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Wouter Sioen <wouter.sioen@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     * @todo Remove this when Fork has no custom Kernel class anymore
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('AppKernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Fully empties the test database
     *
     * @param \SpoonDatabase $database
     */
    protected function emptyTestDatabase($database)
    {
        foreach ($database->getTables() as $table) {
            $database->drop($table);
        }
    }

    /**
     * Copies the parameters.yml file to a backup version
     *
     * @param string $kernelDir
     */
    protected function backupParametersFile($kernelDir)
    {
        $fs = new FileSystem();
        if ($fs->exists($kernelDir . '/config/parameters.yml')) {
            $fs->copy(
                $kernelDir . '/config/parameters.yml',
                $kernelDir . '/config/parameters.yml~backup'
            );
        }
    }

    /**
     * Puts the backed up parameters.yml file back
     *
     * @param string $kernelDir
     */
    protected function putParametersFileBack($kernelDir)
    {
        $fs = new FileSystem();
        if ($fs->exists($kernelDir . '/config/parameters.yml~backup')) {
            $fs->copy(
                $kernelDir . '/config/parameters.yml~backup',
                $kernelDir . '/config/parameters.yml',
                true
            );
            $fs->remove($kernelDir . '/config/parameters.yml~backup');
        }
    }
}
