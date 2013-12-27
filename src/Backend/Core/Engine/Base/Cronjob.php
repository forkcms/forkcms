<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This is the base-object for cronjobs. The module-specific cronjob-files can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Cronjob extends Object
{
    /**
     * The current id
     *
     * @var int
     */
    protected $id;

    /**
     * Clear/removed the busy file
     */
    protected function clearBusyFile()
    {
        // build path
        $path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

        // remove the file
        $fs = new Filesystem();
        $fs->remove($path);
    }

    public function execute()
    {
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return strtolower($this->getModule() . '_' . $this->getAction());
    }

    /**
     * Set the action
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $action The action to load.
     * @param string[optional] $module The module to load.
     */
    public function setAction($action, $module = null)
    {
        // set module
        if($module !== null) $this->setModule($module);

        // check if module is set
        if($this->getModule() === null) throw new BackendException('Module has not yet been set.');

        // path to look for actions based on the module
        if($this->getModule() == 'core') $path = BACKEND_CORE_PATH . '/cronjobs';
        else $path = BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs';

        // check if file exists
        if(!is_file($path . '/' . $action . '.php')) {
            SpoonHTTP::setHeadersByCode(403);
            throw new BackendException('Action not allowed.');
        }

        // set property
        $this->action = (string) $action;
    }

    /**
     * Set the busy file
     */
    protected function setBusyFile()
    {
        // do not set busy file in debug mode
        if(SPOON_DEBUG) return;

        // build path
        $fs = new Filesystem();
        $path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

        // init var
        $isBusy = false;

        // does the busy file already exists.
        if($fs->exists($path)) {
            $isBusy = true;

            // grab counter
            $counter = (int) file_get_contents($path);

            // check the counter
            if($counter > 9) {
                // build class name
                $className = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_cronjob_' . $this->getAction());

                // notify user
                throw new BackendException('Cronjob (' . $className . ') is still busy after 10 runs, check it out!');
            }
        }

        // set counter
        else $counter = 0;

        // increment counter
        $counter++;

        // store content
        $fs->dumpFile($path, $counter);

        // if the cronjob is busy we should NOT proceed
        if($isBusy) exit;
    }

    /**
     * Set the module
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if(!in_array($module, $modules)) {
            // set correct headers
            SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }
}
