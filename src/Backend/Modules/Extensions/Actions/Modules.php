<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;

/**
 * This is the modules-action, it will display the overview of modules.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
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
    private $installedModules = array();
    private $installableModules = array();

    /**
     * Execute the action.
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->loadDataGridInstalled();
        $this->loadDataGridInstallable();

        $this->parse();
        $this->display();
    }

    /**
     * Load the data for the 2 data grids.
     */
    private function loadData()
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

    /**
     * Load the data grid for installable modules.
     */
    private function loadDataGridInstallable()
    {
        // create datagrid
        $this->dataGridInstallableModules = new BackendDataGridArray($this->installableModules);

        $this->dataGridInstallableModules->setSortingColumns(array('raw_name'));
        $this->dataGridInstallableModules->setHeaderLabels(array('raw_name' => \SpoonFilter::ucfirst(BL::getLabel('Name'))));
        $this->dataGridInstallableModules->setColumnsHidden(array('installed', 'name', 'cronjobs_active'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('DetailModule')) {
            $this->dataGridInstallableModules->setColumnURL('raw_name', BackendModel::createURLForAction('DetailModule') . '&amp;module=[raw_name]');
            $this->dataGridInstallableModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('DetailModule') . '&amp;module=[raw_name]', BL::lbl('Details'));
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('InstallModule')) {
            // add install column
            $this->dataGridInstallableModules->addColumn('install', null, BL::lbl('Install'), BackendModel::createURLForAction('InstallModule') . '&amp;module=[raw_name]', BL::lbl('Install'));
            $this->dataGridInstallableModules->setColumnConfirm('install', sprintf(BL::msg('ConfirmModuleInstall'), '[raw_name]'), null, \SpoonFilter::ucfirst(BL::lbl('Install')) . '?');
        }
    }

    /**
     * Load the data grid for installed modules.
     */
    private function loadDataGridInstalled()
    {
        // create datagrid
        $this->dataGridInstalledModules = new BackendDataGridArray($this->installedModules);

        $this->dataGridInstalledModules->setSortingColumns(array('name'));
        $this->dataGridInstalledModules->setColumnsHidden(array('installed', 'raw_name', 'cronjobs_active'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('DetailModule')) {
            $this->dataGridInstalledModules->setColumnURL('name', BackendModel::createURLForAction('DetailModule') . '&amp;module=[raw_name]');
            $this->dataGridInstalledModules->addColumn('details', null, BL::lbl('Details'), BackendModel::createURLForAction('DetailModule') . '&amp;module=[raw_name]', BL::lbl('Details'));
        }

        // add the greyed out option to modules that have warnings
        $this->dataGridInstalledModules->addColumn('hidden');
        $this->dataGridInstalledModules->setColumnFunction(array(new BackendExtensionsModel(), 'hasModuleWarnings'), array('[raw_name]'), array('hidden'));
    }

    /**
     * Parse the datagrids and the reports.
     */
    protected function parse()
    {
        parent::parse();

        // parse data grid
        $this->tpl->assign('dataGridInstallableModules', (string) $this->dataGridInstallableModules->getContent());
        $this->tpl->assign('dataGridInstalledModules', (string) $this->dataGridInstalledModules->getContent());

        // parse installer warnings
        $this->tpl->assign('warnings', (array) \SpoonSession::get('installer_warnings'));
    }
}
