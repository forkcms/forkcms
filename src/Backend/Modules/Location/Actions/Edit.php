<?php

namespace Backend\Modules\Location\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use App\Component\Locale\BackendLanguage;
use Backend\Core\Engine\Model as BackendModel;
use App\Form\Type\Backend\DeleteType;
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
    protected $settings = [];

    /**
     * The settings form
     *
     * @var BackendForm
     */
    protected $settingsForm;

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exists
        if ($this->id !== 0 && BackendLocationModel::exists($this->id)) {
            $this->header->addJS(FrontendLocationModel::getPathToMapStyles());
            parent::execute();

            // define Google Maps API key
            $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');

            // check Google Maps API key, otherwise redirect to settings
            if ($apikey === null) {
                $this->redirect(BackendModel::createUrlForAction('Index', 'Settings'));
            }

            // add js
            $this->header->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey);

            $this->loadData();

            $this->loadForm();
            $this->validateForm();
            $this->loadDeleteForm();

            $this->loadSettingsForm();

            $this->parse();
            $this->display();
        } else {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }
    }

    private function loadData(): void
    {
        $this->record = (array) BackendLocationModel::get($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if (empty($this->record)) {
            $this->redirect(BackendModel::createUrlForAction('Index') . '&error=non-existing');
        }

        $this->settings = BackendLocationModel::getMapSettings($this->id);

        // load the settings from the general settings
        if (empty($this->settings)) {
            $settings = $this->get('fork.settings')->getForModule('Location');

            $this->settings['width'] = $settings['width_widget'];
            $this->settings['height'] = $settings['height_widget'];
            $this->settings['map_type'] = $settings['map_type_widget'];
            $this->settings['map_style'] = $settings['map_style_widget'] ?? 'standard';
            $this->settings['zoom_level'] = $settings['zoom_level_widget'];
            $this->settings['center']['lat'] = $this->record['lat'];
            $this->settings['center']['lng'] = $this->record['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $this->record['lat'];
            $this->settings['center']['lng'] = $this->record['lng'];
        }

        $this->settings['full_url'] = $this->settings['full_url'] ?? false;
        $this->settings['directions'] = $this->settings['directions'] ?? false;
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('edit');
        $this->form->addText('title', $this->record['title'], null, 'form-control title', 'form-control danger title')->makeRequired();
        $this->form->addText('street', $this->record['street'])->makeRequired();
        $this->form->addText('number', $this->record['number'])->makeRequired();
        $this->form->addText('zip', $this->record['zip'])->makeRequired();
        $this->form->addText('city', $this->record['city'])->makeRequired();
        $this->form->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BackendLanguage::getInterfaceLanguage()), $this->record['country'])->makeRequired();
        $this->form->addHidden('redirect', 'overview');
    }

    protected function loadSettingsForm(): void
    {
        $mapTypes = [
            'ROADMAP' => BackendLanguage::lbl('Roadmap', $this->getModule()),
            'SATELLITE' => BackendLanguage::lbl('Satellite', $this->getModule()),
            'HYBRID' => BackendLanguage::lbl('Hybrid', $this->getModule()),
            'TERRAIN' => BackendLanguage::lbl('Terrain', $this->getModule()),
            'STREET_VIEW' => BackendLanguage::lbl('StreetView', $this->getModule()),
        ];
        $mapStyles = [
            'standard' => BackendLanguage::lbl('Default', $this->getModule()),
            'custom' => BackendLanguage::lbl('Custom', $this->getModule()),
            'gray' => BackendLanguage::lbl('Gray', $this->getModule()),
            'blue' => BackendLanguage::lbl('Blue', $this->getModule()),
        ];

        $zoomLevels = array_combine(
            array_merge(['auto'], range(1, 18)),
            array_merge([BackendLanguage::lbl('Auto', $this->getModule())], range(1, 18))
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
            $this->settings['map_style'] ?? null
        );
        $this->settingsForm->addCheckbox('full_url', $this->settings['full_url']);
        $this->settingsForm->addCheckbox('directions', $this->settings['directions']);
        $this->settingsForm->addCheckbox('marker_overview', $this->record['show_overview']);
    }

    protected function parse(): void
    {
        parent::parse();

        // assign to template
        $this->template->assign('item', $this->record);
        $this->template->assign('settings', $this->settings);
        $this->template->assign('godUser', BackendAuthentication::getUser()->isGod());

        $this->settingsForm->parse($this->template);

        // assign message if address was not be geocoded
        if ($this->record['lat'] == null || $this->record['lng'] == null) {
            $this->template->assign('errorMessage', BackendLanguage::err('AddressCouldNotBeGeocoded'));
        }
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BackendLanguage::err('TitleIsRequired'));
            $this->form->getField('street')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('number')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('zip')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('city')->isFilled(BackendLanguage::err('FieldIsRequired'));

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['id'] = $this->id;
                $item['language'] = BackendLanguage::getWorkingLanguage();
                $item['extra_id'] = $this->record['extra_id'];
                $item['title'] = $this->form->getField('title')->getValue();
                $item['street'] = $this->form->getField('street')->getValue();
                $item['number'] = $this->form->getField('number')->getValue();
                $item['zip'] = $this->form->getField('zip')->getValue();
                $item['city'] = $this->form->getField('city')->getValue();
                $item['country'] = $this->form->getField('country')->getValue();

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
                if ($this->form->getField('redirect')->getValue() == 'overview') {
                    $this->redirect(BackendModel::createUrlForAction('Index') . '&report=edited&var=' . rawurlencode($item['title']) . '&highlight=row-' . $item['id']);
                } else {
                    $this->redirect(BackendModel::createUrlForAction('Edit') . '&id=' . $item['id'] . '&report=edited');
                }
            }
        }
    }

    private function loadDeleteForm(): void
    {
        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->record['id']],
            ['module' => $this->getModule()]
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
