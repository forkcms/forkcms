<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Component\Finder\Finder;

/**
 * This is the theme install-action.
 * It will install the theme given via the "theme" GET parameter.
 */
class InstallTheme extends BackendBaseActionIndex
{
    /**
     * Theme we ant to install.
     *
     * @var string
     */
    private $currentTheme;

    public function execute(): void
    {
        $this->checkToken();

        // get parameters
        $this->currentTheme = $this->getRequest()->query->get('theme', '');

        // does the item exist
        if ($this->currentTheme !== '' && BackendExtensionsModel::existsTheme($this->currentTheme)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // make sure this theme can be installed
            $this->validateThatTheThemeCanBeInstalled();

            try {
                // do the actual install
                BackendExtensionsModel::installTheme($this->currentTheme);

                // redirect to index with a success message
                $this->redirect(BackendModel::createUrlForAction('Themes') . '&report=theme-installed&var=' . $this->currentTheme);
            } catch (Exception $e) {
                // redirect to index with a success message
                $this->redirect(BackendModel::createUrlForAction('Themes') . '&report=information-file-is-empty&var=' . $this->currentTheme);
            }
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=non-existing');
        }
    }

    private function validateThatTheThemeCanBeInstalled(): void
    {
        // already installed
        if (BackendExtensionsModel::isThemeInstalled($this->currentTheme)) {
            $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=already-installed&var=' . $this->currentTheme);
        }

        // no information file present
        if (!is_file(FRONTEND_PATH . '/Themes/' . $this->currentTheme . '/info.xml')) {
            $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=no-information-file&var=' . $this->currentTheme);
        }

        $finder = new Finder();

        $finder->in(FRONTEND_PATH . '/Themes/' . $this->currentTheme)->files()->name('*.tpl');

        if ($finder->count() > 0) {
            $this->redirect(BackendModel::createUrlForAction('Themes') . '&error=incompatible-theme&var=' . $this->currentTheme);
        }
    }
}
