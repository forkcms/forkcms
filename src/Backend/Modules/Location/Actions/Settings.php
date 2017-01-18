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
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the settings-action, it will display a form to set general location settings
 */
class Settings extends BackendBaseActionEdit
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
     * Loads the settings form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('settings');

        // add map info (widgets)
        $this->frm->addDropdown('zoom_level_widget', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), $this->get('fork.settings')->get($this->URL->getModule(), 'zoom_level_widget', 13));
        $this->frm->addText('width_widget', $this->get('fork.settings')->get($this->URL->getModule(), 'width_widget'));
        $this->frm->addText('height_widget', $this->get('fork.settings')->get($this->URL->getModule(), 'height_widget'));
        $this->frm->addDropdown(
            'map_type_widget',
            array(
                'ROADMAP' => BL::lbl('Roadmap', $this->getModule()),
                'SATELLITE' => BL::lbl('Satellite', $this->getModule()),
                'HYBRID' => BL::lbl('Hybrid', $this->getModule()),
                'TERRAIN' => BL::lbl('Terrain', $this->getModule()),
                'STREET_VIEW' => BL::lbl('StreetView', $this->getModule()),
            ),
            $this->get('fork.settings')->get(
                $this->URL->getModule(),
                'map_type_widget',
                'roadmap'
            )
        );
    }

    /**
     * Parse the data
     */
    protected function parse()
    {
        parent::parse();
        $this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            if ($this->frm->isCorrect()) {
                // set the base values
                $width = (int) $this->frm->getField('width_widget')->getValue();
                $height = (int) $this->frm->getField('height_widget')->getValue();

                if ($width > 800) {
                    $width = 800;
                } elseif ($width < 300) {
                    $width = $this->get('fork.settings')->get('Location', 'width_widget');
                }
                if ($height < 150) {
                    $height = $this->get('fork.settings')->get('Location', 'height_widget');
                }

                // set our settings (widgets)
                $this->get('fork.settings')->set($this->URL->getModule(), 'zoom_level_widget', (string) $this->frm->getField('zoom_level_widget')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'width_widget', $width);
                $this->get('fork.settings')->set($this->URL->getModule(), 'height_widget', $height);
                $this->get('fork.settings')->set($this->URL->getModule(), 'map_type_widget', (string) $this->frm->getField('map_type_widget')->getValue());

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }
}
