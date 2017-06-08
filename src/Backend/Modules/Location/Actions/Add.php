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
        $this->frm = new BackendForm('add');
        $this->frm->addText('title', null, null, 'form-control title', 'form-control danger title');
        $this->frm->addTextarea('address');
        $this->frm->addDropdown('country', Intl::getRegionBundle()->getCountryNames(BL::getInterfaceLanguage()), 'BE');
    }

    private function validateForm(): void
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
            $this->frm->getField('address')->isFilled(BL::err('FieldIsRequired'));

            if ($this->frm->isCorrect()) {
                // build item
                $item = [];
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['address'] = $this->frm->getField('address')->getValue();
                $item['country'] = $this->frm->getField('country')->getValue();

                // define coordinates
                $coordinates = BackendLocationModel::getCoordinates(
                    $item['address'],
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
