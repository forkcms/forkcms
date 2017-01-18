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
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Symfony\Component\Intl\Intl as Intl;

/**
 * This is the add-action, it will display a form to create a new item
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
        $this->frm->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->frm->addText('street');
        $this->frm->addText('number');
        $this->frm->addText('zip');
        $this->frm->addText('city');
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), 'BE');
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
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['street'] = $this->frm->getField('street')->getValue();
                $item['number'] = $this->frm->getField('number')->getValue();
                $item['zip'] = $this->frm->getField('zip')->getValue();
                $item['city'] = $this->frm->getField('city')->getValue();
                $item['country'] = $this->frm->getField('country')->getValue();

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

                // insert the item
                $item['id'] = BackendLocationModel::insert($item);

                // redirect
                $this->redirect(
                    BackendModel::createURLForAction('Edit') . '&id=' . $item['id'] .
                    '&report=added&var=' . rawurlencode($item['title'])
                );
            }
        }
    }
}
