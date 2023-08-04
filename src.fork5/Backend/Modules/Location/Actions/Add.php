<?php

namespace Backend\Modules\Location\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use ForkCMS\Utility\Geolocation;
use Symfony\Component\Intl\Intl as Intl;

/**
 * This is the add-action, it will display a form to create a new item
 */
class Add extends BackendBaseActionAdd
{
    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->form = new BackendForm('add');
        $this->form->addText('title', null, null, 'form-control title', 'form-control danger title')->makeRequired();
        $this->form->addText('street')->makeRequired();
        $this->form->addText('number')->makeRequired();
        $this->form->addText('zip')->makeRequired();
        $this->form->addText('city')->makeRequired();
        $this->form->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), 'BE')->makeRequired();
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // validate fields
            $this->form->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->form->getField('street')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('number')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('zip')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('city')->isFilled(BL::err('FieldIsRequired'));

            if ($this->form->isCorrect()) {
                // build item
                $item = [];
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->form->getField('title')->getValue();
                $item['street'] = $this->form->getField('street')->getValue();
                $item['number'] = $this->form->getField('number')->getValue();
                $item['zip'] = $this->form->getField('zip')->getValue();
                $item['city'] = $this->form->getField('city')->getValue();
                $item['country'] = $this->form->getField('country')->getValue();

                // define coordinates
                $coordinates = BackendModel::get(Geolocation::class)->getCoordinates(
                    $item['street'],
                    $item['number'],
                    $item['city'],
                    $item['zip'],
                    $item['country']
                );

                // define latitude and longitude
                $item['lat'] = (float) $coordinates['latitude'];
                $item['lng'] = (float) $coordinates['longitude'];

                // insert the item
                $item['id'] = BackendLocationModel::insert($item);

                $generalSettings = $this->get('fork.settings')->getForModule('Location');
                $center = ['lat' => $item['lat'], 'lng' => $item['lng']];
                BackendLocationModel::setMapSetting($item['id'], 'zoom_level', (string) $generalSettings['zoom_level']);
                BackendLocationModel::setMapSetting($item['id'], 'map_type', (string) $generalSettings['map_type']);
                BackendLocationModel::setMapSetting($item['id'], 'map_style', (string) 'standard');
                BackendLocationModel::setMapSetting($item['id'], 'center', $center);
                BackendLocationModel::setMapSetting($item['id'], 'height', (int)  $generalSettings['height']);
                BackendLocationModel::setMapSetting($item['id'], 'width', (int)  $generalSettings['width']);
                BackendLocationModel::setMapSetting($item['id'], 'directions', false);
                BackendLocationModel::setMapSetting($item['id'], 'full_url', false);

                // redirect
                $this->redirect(
                    BackendModel::createUrlForAction('Edit') . '&id=' . $item['id'] .
                    '&report=added&var=' . rawurlencode($item['title'])
                );
            }
        }
    }
}
