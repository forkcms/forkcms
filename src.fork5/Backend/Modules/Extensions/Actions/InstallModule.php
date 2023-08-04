<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is the module install-action.
 * It will install the module given via the "module" GET parameter.
 */
class InstallModule extends BackendBaseActionIndex
{
    /**
     * Module we want to install.
     *
     * @var string
     */
    private $currentModule;

    public function execute(): void
    {
        $this->checkToken();

        // get parameters
        $this->currentModule = $this->getRequest()->query->get('module', '');

        // does the item exist
        if ($this->currentModule !== '' && BackendExtensionsModel::existsModule($this->currentModule)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // make sure this module can be installed
            $this->validateIfModuleCanBeInstalled();

            // do the actual install
            BackendExtensionsModel::installModule($this->currentModule);

            // remove our container cache after this request
            $filesystem = new Filesystem();
            $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir'));

            // redirect to index with a success message
            $this->redirect(BackendModel::createUrlForAction('Modules') . '&report=module-installed&var=' . $this->currentModule . '&highlight=row-module_' . $this->currentModule);
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Modules') . '&error=non-existing');
        }
    }

    private function validateIfModuleCanBeInstalled(): void
    {
        // already installed
        if (BackendModel::isModuleInstalled($this->currentModule)) {
            $this->redirect(BackendModel::createUrlForAction('Modules') . '&error=already-installed&var=' . $this->currentModule);
        }

        // no installer class present
        if (!is_file(BACKEND_MODULES_PATH . '/' . $this->currentModule . '/Installer/Installer.php')) {
            $this->redirect(BackendModel::createUrlForAction('Modules') . '&error=no-installer-file&var=' . $this->currentModule);
        }
    }
}
