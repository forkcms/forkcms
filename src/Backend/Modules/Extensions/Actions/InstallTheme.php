<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the theme install-action.
 * It will install the theme given via the "theme" GET parameter.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class InstallTheme extends BackendBaseActionIndex
{
    /**
     * Theme we ant to install.
     *
     * @var string
     */
    private $currentTheme;

    /**
     * Execute the action.
     */
    public function execute()
    {
        // get parameters
        $this->currentTheme = $this->getParameter('theme', 'string');

        // does the item exist
        if ($this->currentTheme !== null && BackendExtensionsModel::existsTheme($this->currentTheme)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // make sure this theme can be installed
            $this->validateInstall();

            try {
                // do the actual install
                BackendExtensionsModel::installTheme($this->currentTheme);

                // redirect to index with a success message
                $this->redirect(BackendModel::createURLForAction('Themes') . '&report=theme-installed&var=' . $this->currentTheme);
            } catch (Exception $e) {
                // redirect to index with a success message
                $this->redirect(BackendModel::createURLForAction('Themes') . '&report=information-file-is-empty&var=' . $this->currentTheme);
            }
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createURLForAction('Themes') . '&error=non-existing');
        }
    }

    /**
     * Validate if the theme can be installed.
     */
    private function validateInstall()
    {
        // already installed
        if (BackendExtensionsModel::isThemeInstalled($this->currentTheme)) {
            $this->redirect(BackendModel::createURLForAction('Themes') . '&error=already-installed&var=' . $this->currentTheme);
        }

        // no information file present
        if (!is_file(FRONTEND_PATH . '/Themes/' . $this->currentTheme . '/info.xml')) {
            $this->redirect(BackendModel::createURLForAction('Themes') . '&error=no-information-file&var=' . $this->currentTheme);
        }
    }
}
