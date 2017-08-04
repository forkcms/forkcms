<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the modules-action, it will display the overview of modules.
 */
class Modules extends BackendBaseActionIndex
{
    /**
     * Data grids.
     *
     * @var BackendDataGridArray
     */
    private $dataGridInstalledModules;

    /**
     * Data grids.
     *
     * @var BackendDataGridArray
     */
    private $dataGridInstallableModules;

    /**
     * Modules that are or or not installed.
     * This is used as a source for the data grids.
     *
     * @var array
     */
    private $installedModules = [];
    private $installableModules = [];

    public function execute(): void
    {
        parent::execute();

        $this->loadData();
        $this->loadDataGridInstalledModules();
        $this->loadDataGridInstallableModules();

        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        // get all manageable modules
        $modules = BackendExtensionsModel::getModules();

        // split the modules in 2 separate data grid sources
        foreach ($modules as $module) {
            if ($module['installed']) {
                $this->installedModules[] = $module;
            } else {
                $this->installableModules[] = $module;
            }
        }
    }

    private function loadDataGridInstallableModules(): void
    {
        // create datagrid
        $this->dataGridInstallableModules = new BackendDataGridArray($this->installableModules);

        $this->dataGridInstallableModules->setSortingColumns(['raw_name']);
        $this->dataGridInstallableModules->setHeaderLabels(['raw_name' => \SpoonFilter::ucfirst(BL::getLabel('Name'))]);
        $this->dataGridInstallableModules->setColumnsHidden(['installed', 'name']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('DetailModule')) {
            $this->dataGridInstallableModules->setColumnURL('raw_name', BackendModel::createUrlForAction('DetailModule') . '&amp;module=[raw_name]');
            $this->dataGridInstallableModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createUrlForAction('DetailModule') . '&amp;module=[raw_name]', BL::lbl('Details'));
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('InstallModule')) {
            // add install column
            $this->dataGridInstallableModules->addColumn('install', null, BL::lbl('Install'), BackendModel::createUrlForAction('InstallModule') . '&amp;module=[raw_name]', BL::lbl('Install'));
            $this->dataGridInstallableModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'), null, \SpoonFilter::ucfirst(BL::lbl('Install')) . '?');
        }
    }

    private function loadDataGridInstalledModules(): void
    {
        // create datagrid
        $this->dataGridInstalledModules = new BackendDataGridArray($this->installedModules);

        $this->dataGridInstalledModules->setSortingColumns(['name']);
        $this->dataGridInstalledModules->setColumnsHidden(['installed', 'raw_name']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('DetailModule')) {
            $this->dataGridInstalledModules->setColumnURL('name', BackendModel::createUrlForAction('DetailModule') . '&amp;module=[raw_name]');
            $this->dataGridInstalledModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createUrlForAction('DetailModule') . '&amp;module=[raw_name]', BL::lbl('Details'));
        }

        // add the greyed out option to modules that have warnings
        $this->dataGridInstalledModules->addColumn('hidden');
        $this->dataGridInstalledModules->setColumnFunction([new BackendExtensionsModel(), 'hasModuleWarnings'], ['[raw_name]'], ['hidden']);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse data grid
        $this->template->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
        $this->template->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());

        // parse installer warnings
        $this->template->assign('warnings', (array) BackendModel::getSession()->get('installer_warnings'));
    }
}
