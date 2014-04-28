<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * This is the index-action (default), it will display the overview of location items
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The settings form
     *
     * @var BackendForm
     */
    protected $form;

    /**
     * @var array
     */
    protected $items = array(), $settings = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('http://maps.google.com/maps/api/js?sensor=false', null, false, true, false);

        $this->loadData();

        $this->loadDataGrid();
        $this->loadSettingsForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the settings
     */
    protected function loadData()
    {
        $this->items = BackendLocationModel::getAll();
        $this->settings = BackendLocationModel::getMapSettings(0);
        $firstMarker = current($this->items);

        // if there are no markers we reset it to the birthplace of Fork
        if ($firstMarker === false) $firstMarker = array('lat' => '51.052146', 'lng' => '3.720491');

        // load the settings from the general settings
        if (empty($this->settings)) {
            $this->settings = BackendModel::getModuleSettings('Location');

            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }
    }

    /**
     * Loads the datagrid
     */
    private function loadDataGrid()
    {
        $this->dataGrid = new BackendDataGridDB(
            BackendLocationModel::QRY_DATAGRID_BROWSE,
            array(BL::getWorkingLanguage())
        );
        $this->dataGrid->setSortingColumns(array('address', 'title'), 'address');
        $this->dataGrid->setSortParameter('ASC');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'title', BackendModel::createURLForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit', null, BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]', BL::lbl('Edit')
            );
        }
    }

    /**
     * Load the settings form
     */
    protected function loadSettingsForm()
    {
        $mapTypes = array(
            'ROADMAP' => BL::lbl('Roadmap', $this->getModule()),
            'SATELLITE' => BL::lbl('Satellite', $this->getModule()),
            'HYBRID' => BL::lbl('Hybrid', $this->getModule()),
            'TERRAIN' => BL::lbl('Terrain', $this->getModule())
        );

        $zoomLevels = array_combine(
            array_merge(array('auto'), range(3, 18)),
            array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))
        );

        $this->form = new BackendForm('settings');

        // add map info (overview map)
        $this->form->addHidden('map_id', 0);
        $this->form->addDropdown('zoom_level', $zoomLevels, $this->settings['zoom_level']);
        $this->form->addText('width', $this->settings['width']);
        $this->form->addText('height', $this->settings['height']);
        $this->form->addDropdown('map_type', $mapTypes, $this->settings['map_type']);
    }

    /**
     * Parse the datagrid
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
        $this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());

        // assign to template
        $this->tpl->assign('items', $this->items);
        $this->tpl->assign('settings', $this->settings);
        $this->form->parse($this->tpl);
    }
}
