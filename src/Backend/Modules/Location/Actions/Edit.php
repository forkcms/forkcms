<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * This is the edit-action, it will display a form to create a new item
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
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
        parent::execute();

        $this->loadData();
        $this->loadForm();
        $this->validateForm();
        $this->loadSettingsForm();
        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function loadData()
    {
        $this->id = $this->getParameter('id', 'int');
        $this->record = BackendLocationModel::get($this->id);

        // no item found, throw an exception, because somebody is fucking with our URL
        if ($this->id == null || empty($this->record)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        $this->settings = $this->record->getSettings();

        // load the settings from the general settings
        if (empty($this->settings)) {
            $settings = BackendModel::getModuleSettings('Location');
            $this->settings = array(
                'width' => $settings['width_widget'],
                'height' => $settings['height_widget'],
                'map_type' => $settings['map_type_widget'],
                'zoom_level' => $settings['zoom_level_widget'],
                'center' => array(
                    'lat' => $this->record->getLat(),
                    'lng' => $this->record->getLng()
                )
            );
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $this->record->getLat();
            $this->settings['center']['lng'] = $this->record->getLng();
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
        $this->frm->addText('title', $this->record->getTitle(), null, 'inputText title', 'inputTextError title');
        $this->frm->addText('street', $this->record->getStreet());
        $this->frm->addText('number', $this->record->getNumber());
        $this->frm->addText('zip', $this->record->getZip());
        $this->frm->addText('city', $this->record->getCity());
        $this->frm->addDropdown('country', \SpoonLocale::getCountries(BL::getInterfaceLanguage()), $this->record->getCountry());
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
            'TERRAIN' => BL::lbl('Terrain', $this->getModule())
        );

        $zoomLevels = array_combine(
            array_merge(array('auto'), range(3, 18)),
            array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))
        );

        $this->settingsForm = new BackendForm('settings');

        // add map info (overview map)
        $this->settingsForm->addHidden('map_id', $this->id);
        $this->settingsForm->addDropdown('zoom_level', $zoomLevels, $this->settings['zoom_level']);
        $this->settingsForm->addText('width', $this->settings['width']);
        $this->settingsForm->addText('height', $this->settings['height']);
        $this->settingsForm->addDropdown('map_type', $mapTypes, $this->settings['map_type']);
        $this->settingsForm->addCheckbox('full_url', $this->settings['full_url']);
        $this->settingsForm->addCheckbox('directions', $this->settings['directions']);
        $this->settingsForm->addCheckbox('marker_overview', $this->record->getShowOverview());
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        $this->header->addJS('http://maps.google.com/maps/api/js?sensor=false', null, false, true, false);

        // assign to template
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('settings', $this->settings);
        $this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());

        $this->settingsForm->parse($this->tpl);

        // assign message if address was not be geocoded
        if ($this->record->getLat() == null || $this->record->getLng() == null) {
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

                // check if it's necessary to geocode again
                $reGeocode = ($this->record->getLat() === null
                    || $this->record->getLng() === null
                    || $this->frm->getField('street')->getValue() != $this->record->getStreet()
                    || $this->frm->getField('number')->getValue() != $this->record->getNumber()
                    || $this->frm->getField('zip')->getValue() != $this->record->getZip()
                    || $this->frm->getField('city')->getValue() != $this->record->getCity()
                    || $this->frm->getField('country')->getValue() != $this->record->getCountry()
                ) ? true : false;

                // build item
                $item = $this->record;
                $item
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setTitle($this->frm->getField('title')->getValue())
                    ->setStreet($this->frm->getField('street')->getValue())
                    ->setNumber($this->frm->getField('number')->getValue())
                    ->setZip($this->frm->getField('zip')->getValue())
                    ->setCity($this->frm->getField('city')->getValue())
                    ->setCountry($this->frm->getField('country')->getValue())
                ;

                // should we geocode again
                if ($reGeocode) {
                    // define coordinates
                    $coordinates = BackendModel::get('geocoder')->getCoordinates($item);

                    // define latitude and longitude
                    $item->setLat($coordinates['latitude']);
                    $item->setLng($coordinates['longitude']);
                }

                // update the item
                BackendLocationModel::update($item);

                // everything is saved, so redirect to the overview
                if ($item->getLat() && $item->getLng()) {
                    // trigger event
                    BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));
                }

                if ($this->frm->getField('redirect')->getValue() == 'overview') {
                    $this->redirect(
                        BackendModel::createURLForAction('Index') . '&report=edited&var=' . urlencode($item->getTitle()) .
                       '&highlight=row-' . $item->getId());
                } else {
                    $this->redirect(BackendModel::createURLForAction('Edit') . '&id=' . $item->getId() . '&report=edited');
                }
            }
        }
    }
}
