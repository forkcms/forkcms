<?php

namespace Install\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use Backend\Core\Installer\CoreInstaller;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * Step 7 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Step7 extends Step
{
    /**
     * Create locale cache files
     */
    private function createLocaleFiles()
    {
        // all available languages
        $languages = array_unique(
            array_merge(\SpoonSession::get('languages'), \SpoonSession::get('interface_languages'))
        );

        // loop all the languages
        foreach ($languages as $language) {
            // get applications
            $applications = $this->getContainer()->get('database')->getColumn(
                'SELECT DISTINCT application
                 FROM locale
                 WHERE language = ?',
                array((string) $language)
            );

            // loop applications
            foreach ((array) $applications as $application) {
                // build application locale cache
                BackendLocaleModel::buildCache($language, $application);
            }
        }
    }

    /**
     * Define paths also used in frontend/backend, to be used in installer.
     */
    private function definePaths()
    {
        // general paths
        define('BACKEND_PATH', PATH_WWW . '/src/Backend');
        define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');

        define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
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

    /**
     * Executes this step.
     */
    public function execute()
    {
        // extend execution limit
        set_time_limit(0);

        // validate all previous steps
        if (!$this->validateForm()) {
            \SpoonHTTP::redirect('/install?step=1');
        }

        // define paths
        $this->definePaths();

        // delete cached data
        $this->deleteCachedData();

        // install modules
        $this->installModules();

        // create locale cache
        $this->createLocaleFiles();

        // already installed
        $fs = new Filesystem();
        $fs->dumpFile(
            dirname(__FILE__) . '/../Cache/Installed.txt',
            date('Y-m-d H:i:s')
        );

        // show success message
        $this->showSuccess();

        // clear cache, this will remove the cached container
        $command = new CacheClearCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array());
        $output = new NullOutput();
        $resultCode = $command->run($input, $output);

        // install assets
        $command = new AssetsInstallCommand();
        $command->setContainer($this->getContainer());
        $input = new ArrayInput(array('target' => PATH_WWW));
        $output = new NullOutput();
        $resultCode = $command->run($input, $output);

        // clear session
        \SpoonSession::destroy();
    }

    /**
     * Installs the required and optional modules
     */
    private function installModules()
    {
        // The default extras to add to every page after installation of all
        // modules and to add to the default templates.
        $defaultExtras = array();

        // init var
        $warnings = array();

        // put a new instance of the database in the container
        $database = new \SpoonDatabase(
            'mysql',
            \SpoonSession::get('db_hostname'),
            \SpoonSession::get('db_username'),
            \SpoonSession::get('db_password'),
            \SpoonSession::get('db_database'),
            \SpoonSession::get('db_port')
        );
        $database->execute(
            'SET CHARACTER SET :charset, NAMES :charset, time_zone = "+0:00"',
            array('charset' => 'utf8')
        );
        $this->getContainer()->set('database', $database);

        // create the core installer
        $installer = new CoreInstaller(
            $this->getContainer()->get('database'),
            \SpoonSession::get('languages'),
            \SpoonSession::get('interface_languages'),
            \SpoonSession::get('example_data'),
            array(
                 'default_language' => \SpoonSession::get('default_language'),
                 'default_interface_language' => \SpoonSession::get('default_interface_language'),
                 'spoon_debug_email' => \SpoonSession::get('email'),
                 'api_email' => \SpoonSession::get('email'),
                 'site_domain' => (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'fork.local',
                 'site_title' => 'Fork CMS',
                 'smtp_server' => '',
                 'smtp_port' => '',
                 'smtp_username' => '',
                 'smtp_password' => ''
            )
        );

        // install the core
        $installer->install();

        // add the warnings
        $moduleWarnings = $installer->getWarnings();
        if (!empty($moduleWarnings)) {
            $warnings[] = array('module' => 'Core', 'warnings' => $moduleWarnings);
        }

        // add the default extras
        $moduleDefaultExtras = $installer->getDefaultExtras();
        if (!empty($moduleDefaultExtras)) {
            array_merge($defaultExtras, $moduleDefaultExtras);
        }

        // variables passed to module installers
        $variables = array();
        $variables['email'] = \SpoonSession::get('email');
        $variables['default_interface_language'] = \SpoonSession::get('default_interface_language');

        // modules to install (required + selected)
        $modules = array_unique(array_merge($this->modules['required'], \SpoonSession::get('modules')));

        // loop required modules
        foreach ($modules as $module) {
            $class = 'Backend\\Modules\\' . $module . '\\Installer\\Installer';

            // install exists
            if (class_exists($class)) {
                // users module needs custom variables
                if ($module == 'Users') {
                    $variables['password'] = \SpoonSession::get('password');
                }

                // create installer
                /** @var $install ModuleInstaller */
                $installer = new $class(
                    $this->getContainer()->get('database'),
                    \SpoonSession::get('languages'),
                    \SpoonSession::get('interface_languages'),
                    \SpoonSession::get('example_data'),
                    $variables
                );

                // install the module
                $installer->install();

                // add the warnings
                $moduleWarnings = $installer->getWarnings();
                if (!empty($moduleWarnings)) {
                    $warnings[] = array('module' => $module, 'warnings' => $moduleWarnings);
                }

                // add the default extras
                $moduleDefaultExtras = $installer->getDefaultExtras();
                if (!empty($moduleDefaultExtras)) {
                    $defaultExtras = array_merge($defaultExtras, $moduleDefaultExtras);
                }
            }
        }

        // loop default extras
        foreach ($defaultExtras as $extra) {
            // get pages without this extra
            $revisionIds = $this->getContainer()->get('database')->getColumn(
                'SELECT i.revision_id
                 FROM pages AS i
                 WHERE i.revision_id NOT IN (
                     SELECT DISTINCT b.revision_id
                     FROM pages_blocks AS b
                     WHERE b.extra_id = ?
                    GROUP BY b.revision_id
                 )',
                array($extra['id'])
            );

            // build insert array for this extra
            $insertExtras = array();
            foreach ($revisionIds as $revisionId) {
                $insertExtras[] = array(
                    'revision_id' => $revisionId,
                    'position' => $extra['position'],
                    'extra_id' => $extra['id'],
                    'created_on' => gmdate('Y-m-d H:i:s'),
                    'edited_on' => gmdate('Y-m-d H:i:s'),
                    'visible' => 'Y'
                );
            }

            // insert block
            $this->getContainer()->get('database')->insert('pages_blocks', $insertExtras);
        }

        // parse the warnings
        $this->tpl->assign('warnings', $warnings);
    }

    /**
     * Is this step allowed.
     *
     * @return bool
     */
    public static function isAllowed()
    {
        return Step6::isAllowed() && isset($_SESSION['email']) && isset($_SESSION['password']);
    }

    /**
     * Show the success message
     */
    private function showSuccess()
    {
        // assign variables
        $this->tpl->assign('url', (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'fork.local');
        $this->tpl->assign('email', \SpoonSession::get('email'));
        $this->tpl->assign('password', \SpoonSession::get('password'));
    }

    /**
     * Validates the previous steps
     */
    private function validateForm()
    {
        return Step6::isAllowed();
    }
}
