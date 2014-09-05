<?php

namespace ForkCMS\Bundle\InstallerBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\CoreInstaller;
use Backend\Core\Installer\ModuleInstaller;
use Symfony\Component\DependencyInjection\Container;

/**
 * This service installs fork
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class ForkInstaller
{
    /**
     * The root dir of our project
     *
     * @var string
     */
    private $rootDir;

    /**
     * The Dependency injection container
     *
     * @var Container
     */
    private $container;

    /**
     * @todo: make sure the Container doesn't have to be injected
     */
    public function __construct(Container $container, $rootDir)
    {
        $this->container = $container;
        $this->rootDir = $rootDir;
    }

    /**
     * Installs Fork
     *
     * @param  array  $data The collected data required for Fork
     * @return bool         Is Fork successfully installed?
     */
    public function install(array $data)
    {
        if (!$this->isValidData($data)) {
            return false;
        }

        // extend execution limit
        set_time_limit(0);
        ini_set('memory_limit', '16M');

        $this->definePaths();
        $this->deleteCachedData();

        $this->buildDatabase($data);
        $this->installCore($data);
        var_dump('ok');exit;
    }

    /**
     * Checks if our given data is complete and valid
     *
     * @param  array $data The collected data required to install Fork
     * @return bool        Do we have all the needed data
     */
    protected function isValidData(array $data)
    {
        if (
            !in_array('db_hostname', $data)
            || !in_array('db_username', $data)
            || !in_array('db_password', $data)
            || !in_array('db_database', $data)
            || !in_array('db_port', $data)

            || !in_array('languages', $data)
            || !in_array('interface_languages', $data)
            || !in_array('default_language', $data)
            || !in_array('default_interface_language', $data)

            || !in_array('modules', $data)
            || !in_array('example_data', $data)

            || !in_array('email', $data)
            || !in_array('password', $data)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Define paths also used in frontend/backend, to be used in installer.
     * @deprecated This is done in different places in Fork. This should be centralized
     */
    private function definePaths()
    {
        // general paths
        define('BACKEND_PATH', $this->rootDir . 'src/Backend');
        define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');

        define('FRONTEND_PATH', $this->rootDir . 'src/Frontend');
        define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
    }

    /**
     * Delete the cached data
     */
    private function deleteCachedData()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        foreach ($finder->files()->in(BACKEND_CACHE_PATH)->in(FRONTEND_CACHE_PATH) as $file) {
            /** @var $file \SplFileInfo */
            $fs->remove($file->getRealPath());
        }
    }

    protected function installCore($data)
    {
        // install the core
        $installer = $this->getCoreInstaller($data);
        $installer->install();
    }

    protected function buildDatabase($data)
    {
        // put a new instance of the database in the container
        $database = new \SpoonDatabase(
            'mysql',
            $data['db_hostname'],
            $data['db_username'],
            $data['db_password'],
            $data['db_database'],
            $data['db_port']
        );
        $database->execute(
            'SET CHARACTER SET :charset, NAMES :charset, time_zone = "+0:00"',
            array('charset' => 'utf8')
        );
        $this->container->set('database', $database);
    }

    protected function getCoreInstaller($data)
    {
        // create the core installer
        return new CoreInstaller(
            $this->container->get('database'),
            $data['languages'],
            $data['interface_languages'],
            $data['example_data'],
            array(
                'default_language'           => $data['default_language'],
                'default_interface_language' => $data['default_interface_language'],
                'spoon_debug_email'          => $data['email'],
                'api_email'                  => $data['email'],
                'site_domain'                => (isset($_SERVER['HTTP_HOST'])) ?
                    $_SERVER['HTTP_HOST'] :
                    'fork.local',
                'site_title'                 => 'Fork CMS',
                'smtp_server'                => '',
                'smtp_port'                  => '',
                'smtp_username'              => '',
                'smtp_password'              => '',
            )
        );
    }
}
