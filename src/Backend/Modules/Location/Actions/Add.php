<?php

namespace Backend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Backend\Modules\Location\Entity\Location;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Mathias Dewelde <mathias@studiorauw.eu>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('add');
        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addText('street');
        $this->frm->addText('number');
        $this->frm->addText('zip');
        $this->frm->addText('city');
        $this->frm->addDropdown('country', \SpoonLocale::getCountries(BL::getInterfaceLanguage()), 'BE');
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
                $location = new Location();
                $location
                    ->setLanguage(BL::getWorkingLanguage())
                    ->setTitle($this->frm->getField('title')->getValue())
                    ->setStreet($this->frm->getField('street')->getValue())
                    ->setNumber($this->frm->getField('number')->getValue())
                    ->setZip($this->frm->getField('zip')->getValue())
                    ->setCity($this->frm->getField('city')->getValue())
                    ->setCountry($this->frm->getField('country')->getValue())
                ;

                // define coordinates
                $coordinates = BackendLocationModel::getCoordinates(
                    $location->getStreet(),
                    $location->getNumber(),
                    $location->getCity(),
                    $location->getZip(),
                    $location->getCountry()
                );

                // define latitude and longitude
                $location->setLat($coordinates['latitude']);
                $location->setLng($coordinates['longitude']);

                // insert the item
                BackendLocationModel::insert($location);

                // everything is saved, so redirect to the overview
                if ($location->getLat() && $location->getLng()) {
                    // trigger event
                    BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $location));
                }

                // redirect
                $this->redirect(
                    BackendModel::createURLForAction('Edit') . '&id=' . $location->getId() .
                    '&report=added&var=' . urlencode($location->getTitle())
                );
            }
        }
    }
}
