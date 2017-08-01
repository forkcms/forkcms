<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the detail-action it will display the details of a module.
 */
class DetailModule extends BackendBaseActionIndex
{
    /**
     * Module we request the details of.
     *
     * @var string
     */
    private $currentModule;

    /**
     * Datagrids.
     *
     * @var BackendDataGridArray
     */
    private $dataGridCronjobs;
    private $dataGridEvents;

    /**
     * Information fetched from the info.xml.
     *
     * @var array
     */
    private $information = [];

    /**
     * List of warnings.
     *
     * @var array
     */
    private $warnings = [];

    public function execute(): void
    {
        // get parameters
        $this->currentModule = $this->getRequest()->query->get('module', '');

        // does the item exist
        if ($this->currentModule !== '' && BackendExtensionsModel::existsModule($this->currentModule)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // load data
            $this->loadData();

            // load datagrids
            $this->loadDataGridEvents();

            // parse
            $this->parse();

            // display the page
            $this->display();
        } else {
            // no item found, redirect to index, because somebody is fucking with our url
            $this->redirect(BackendModel::createUrlForAction('Modules') . '&error=non-existing');
        }
    }

    /**
     * Load the data.
     * This will also set some warnings if needed.
     */
    private function loadData(): void
    {
        // inform that the module is not installed yet
        if (!BackendModel::isModuleInstalled($this->currentModule)) {
            $this->warnings[] = ['message' => BL::getMessage('InformationModuleIsNotInstalled')];
        }

        // fetch the module information
        $moduleInformation = BackendExtensionsModel::getModuleInformation($this->currentModule);
        $this->information = $moduleInformation['data'];
        $this->warnings = $this->warnings + $moduleInformation['warnings'];
    }

    private function loadDataGridEvents(): void
    {
        // no hooks = don't bother
        if (!isset($this->information['events'])) {
            return;
        }

        // create data grid
        $this->dataGridEvents = new BackendDataGridArray($this->information['events']);

        // no paging
        $this->dataGridEvents->setPaging(false);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign module data
        $this->template->assign('name', $this->currentModule);
        $this->template->assign('warnings', $this->warnings);
        $this->template->assign('information', $this->information);
        $this->template->assign('showInstallButton', !BackendModel::isModuleInstalled($this->currentModule) && BackendAuthentication::isAllowedAction('InstallModule'));

        // data grids
        $this->template->assign('dataGridEvents', (isset($this->dataGridEvents) && $this->dataGridEvents->getNumResults() > 0) ? $this->dataGridEvents->getContent() : false);
        $this->template->assign('dataGridCronjobs', (isset($this->dataGridCronjobs) && $this->dataGridCronjobs->getNumResults() > 0) ? $this->dataGridCronjobs->getContent() : false);
    }
}
