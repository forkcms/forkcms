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
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the index-action (default), it will display the overview of location items
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
    protected $items = [];
    protected $settings = [];

    public function execute(): void
    {
        $this->header->addJS(FrontendLocationModel::getPathToMapStyles());
        parent::execute();

        // define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');

        // check Google Maps API key, otherwise redirect to settings
        if ($apikey === null) {
            $this->redirect(BackendModel::createUrlForAction('Index', 'Settings'));
        }

        // add js
        $this->header->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey, null, false, true, false);

        $this->loadData();

        $this->loadDataGrid();
        $this->loadSettingsForm();

        $this->parse();
        $this->display();
    }

    protected function loadData(): void
    {
        $this->items = BackendLocationModel::getAll();
        $this->settings = BackendLocationModel::getMapSettings(0);
        $firstMarker = current($this->items);

        // if there are no markers we reset it to the birthplace of Fork
        if ($firstMarker === false) {
            $firstMarker = ['lat' => '51.052146', 'lng' => '3.720491'];
        }

        // load the settings from the general settings
        if (empty($this->settings)) {
            $this->settings = $this->get('fork.settings')->getForModule('Location');

            $this->settings['map_type'] = $this->settings['map_type_widget'];
            $this->settings['map_style'] = isset($this->settings['map_style_widget']) ? $this->settings['map_style_widget'] : 'standard';

            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }
    }

    private function loadDataGrid(): void
    {
        $this->dataGrid = new BackendDataGridDatabase(
            BackendLocationModel::QUERY_DATAGRID_BROWSE,
            [BL::getWorkingLanguage()]
        );
        $this->dataGrid->setSortingColumns(['address', 'title'], 'address');
        $this->dataGrid->setSortParameter('ASC');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]'
            );
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    protected function loadSettingsForm(): void
    {
        $mapTypes = [
            'ROADMAP' => BL::lbl('Roadmap', $this->getModule()),
            'SATELLITE' => BL::lbl('Satellite', $this->getModule()),
            'HYBRID' => BL::lbl('Hybrid', $this->getModule()),
            'TERRAIN' => BL::lbl('Terrain', $this->getModule()),
            'STREET_VIEW' => BL::lbl('StreetView', $this->getModule()),
        ];
        $mapStyles = [
            'standard' => BL::lbl('Default', $this->getModule()),
            'custom' => BL::lbl('Custom', $this->getModule()),
            'gray' => BL::lbl('Gray', $this->getModule()),
            'blue' => BL::lbl('Blue', $this->getModule()),
        ];

        $zoomLevels = array_combine(
            array_merge(['auto'], range(1, 18)),
            array_merge([BL::lbl('Auto', $this->getModule())], range(1, 18))
        );

        $this->form = new BackendForm('settings');

        // add map info (overview map)
        $this->form->addHidden('map_id', 0);
        $this->form->addDropdown('zoom_level', $zoomLevels, $this->settings['zoom_level']);
        $this->form->addText('width', $this->settings['width']);
        $this->form->addText('height', $this->settings['height']);
        $this->form->addDropdown('map_type', $mapTypes, $this->settings['map_type']);
        $this->form->addDropdown(
            'map_style',
            $mapStyles,
            isset($this->settings['map_style']) ? $this->settings['map_style'] : null
        );
    }

    protected function parse(): void
    {
        parent::parse();

        $this->template->assign('dataGrid', (string) $this->dataGrid->getContent());
        $this->template->assign('godUser', BackendAuthentication::getUser()->isGod());

        // assign to template
        $this->template->assign('items', $this->items);
        $this->template->assign('settings', $this->settings);
        $this->form->parse($this->template);
    }
}
