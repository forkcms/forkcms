<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Symfony\Component\Intl\Intl as Intl;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the edit-action, it will display a form to create a new item
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * The settings form
     *
     * @var BackendForm
     */
    protected $settingsForm;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendLocationModel::exists($this->id)) {
            $this->header->addJS(FrontendLocationModel::getPathToMapStyles());
            parent::execute();

            // define Google Maps API key
            $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');

            // check Google Maps API key, otherwise redirect to settings
            if ($apikey === null) {
                $this->redirect(BackendModel::createURLForAction('Index', 'Settings'));
            }

            // add js
            $this->header->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey, null, false, true, false);

            $this->loadData();

            $this->loadForm();
            $this->validateForm();

            $this->loadSettingsForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data
     */
    private function loadData()
    {
        $this->record = (array) BackendLocationModel::get($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        $this->settings = BackendLocationModel::getMapSettings($this->id);

        // load the settings from the general settings
        if (empty($this->settings)) {
            $settings = $this->get('fork.settings')->getForModule('Location');

            $this->settings['width'] = $settings['width_widget'];
            $this->settings['height'] = $settings['height_widget'];
            $this->settings['map_type'] = $settings['map_type_widget'];
            $this->settings['map_style'] = isset($settings['map_style_widget']) ? $settings['map_style_widget'] : 'standard';
            $this->settings['zoom_level'] = $settings['zoom_level_widget'];
            $this->settings['center']['lat'] = $this->record['lat'];
            $this->settings['center']['lng'] = $this->record['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $this->record['lat'];
            $this->settings['center']['lng'] = $this->record['lng'];
        }

        $this->settings['full_url'] = (isset($this->settings['full_url'])) ? ($this->settings['full_url']) : false;
        $this->settings['directions'] = (isset($this->settings['directions'])) ? ($this->settings['directions']) : false;
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('edit');
        $this->frm->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title');
        $this->frm->addText('street', $this->record['street']);
        $this->frm->addText('number', $this->record['number']);
        $this->frm->addText('zip', $this->record['zip']);
        $this->frm->addText('city', $this->record['city']);
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), $this->record['country']);
        $this->frm->addHidden('redirect', 'overview');
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
            'TERRAIN' => BL::lbl('Terrain', $this->getModule()),
            'STREET_VIEW' => BL::lbl('StreetView', $this->getModule()),
        );
        $mapStyles = array(
            'standard' => BL::lbl('Default', $this->getModule()),
            'custom' => BL::lbl('Custom', $this->getModule()),
            'gray' => BL::lbl('Gray', $this->getModule()),
            'blue' => BL::lbl('Blue', $this->getModule()),
        );

        $zoomLevels = array_combine(
            array_merge(array('auto'), range(1, 18)),
            array_merge(array(BL::lbl('Auto', $this->getModule())), range(1, 18))
        );

        $this->settingsForm = new BackendForm('settings');

        // add map info (overview map)
        $this->settingsForm->addHidden('map_id', $this->id);
        $this->settingsForm->addDropdown('zoom_level', $zoomLevels, $this->settings['zoom_level']);
        $this->settingsForm->addText('width', $this->settings['width']);
        $this->settingsForm->addText('height', $this->settings['height']);
        $this->settingsForm->addDropdown('map_type', $mapTypes, $this->settings['map_type']);
        $this->settingsForm->addDropdown(
            'map_style',
            $mapStyles,
            isset($this->settings['map_style']) ? $this->settings['map_style'] : null
        );
        $this->settingsForm->addCheckbox('full_url', $this->settings['full_url']);
        $this->settingsForm->addCheckbox('directions', $this->settings['directions']);
        $this->settingsForm->addCheckbox('marker_overview', ($this->record['show_overview'] == 'Y'));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign to template
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('settings', $this->settings);
        $this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());

        $this->settingsForm->parse($this->tpl);

        // assign message if address was not be geocoded
        if ($this->record['lat'] == null || $this->record['lng'] == null) {
            $this->tpl->assign('errorMessage', BL::err('AddressCouldNotBeGeocoded'));
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->frm->getField('street')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('number')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('zip')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('city')->isFilled(BL::err('FieldIsRequired'));

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['language'] = BL::getWorkingLanguage();
                $item['extra_id'] = $this->record['extra_id'];
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['street'] = $this->frm->getField('street')->getValue();
                $item['number'] = $this->frm->getField('number')->getValue();
                $item['zip'] = $this->frm->getField('zip')->getValue();
                $item['city'] = $this->frm->getField('city')->getValue();
                $item['country'] = $this->frm->getField('country')->getValue();

                // check if it's necessary to geocode again
                if ($this->record['lat'] === null || $this->record['lng'] === null || $item['street'] != $this->record['street'] || $item['number'] != $this->record['number'] || $item['zip'] != $this->record['zip'] || $item['city'] != $this->record['city'] || $item['country'] != $this->record['country']) {
                    // define coordinates
                    $coordinates = BackendLocationModel::getCoordinates(
                        $item['street'],
                        $item['number'],
                        $item['city'],
                        $item['zip'],
                        $item['country']
                    );

                    // define latitude and longitude
                    $item['lat'] = $coordinates['latitude'];
                    $item['lng'] = $coordinates['longitude'];
                } else {
                    $item['lat'] = $this->record['lat'];
                    $item['lng'] = $this->record['lng'];
                }

                // insert the item
                BackendLocationModel::update($item);

                // redirect to the overview
                if ($this->frm->getField('redirect')->getValue() == 'overview') {
                    $this->redirect(BackendModel::createURLForAction('Index') . '&report=edited&var=' . rawurlencode($item['title']) . '&highlight=row-' . $item['id']);
                } else {
                    $this->redirect(BackendModel::createURLForAction('Edit') . '&id=' . $item['id'] . '&report=edited');
                }
            }
        }
    }
}
