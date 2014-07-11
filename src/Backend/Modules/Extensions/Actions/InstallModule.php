<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the module install-action.
 * It will install the module given via the "module" GET parameter.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class InstallModule extends BackendBaseActionIndex
{
    /**
     * Module we want to install.
     *
     * @var string
     */
    private $currentModule;

    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->currentModule = $this->getParameter('module', 'string');

        // does the item exist
        if ($this->currentModule !== null && BackendExtensionsModel::existsModule($this->currentModule)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // make sure this module can be installed
            $this->validateInstall();

            // do the actual install
            BackendExtensionsModel::installModule($this->currentModule);

            // redirect to index with a success message
            $this->redirect(BackendModel::createURLForAction('Modules') . '&report=module-installed&var=' . $this->currentModule . '&highlight=row-module_' . $this->currentModule);
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createURLForAction('Modules') . '&error=non-existing');
        }
    }

    /**
     * Validate if the module can be installed.
     */
    private function validateInstall()
    {
        // already installed
        if (BackendModel::isModuleInstalled($this->currentModule)) {
            $this->redirect(BackendModel::createURLForAction('Modules') . '&error=already-installed&var=' . $this->currentModule);
        }

        // no installer class present
        if (!is_file(BACKEND_MODULES_PATH . '/' . $this->currentModule . '/Installer/Installer.php')) {
            $this->redirect(BackendModel::createURLForAction('Modules') . '&error=no-installer-file&var=' . $this->currentModule);
        }
    }
}
