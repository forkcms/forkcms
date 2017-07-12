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
        $this->form = new BackendForm('settings');

        // add map info (widgets)
        $this->form->addDropdown('zoom_level_widget', array_combine(array_merge(['auto'], range(3, 18)), array_merge([BL::lbl('Auto', $this->getModule())], range(3, 18))), $this->get('fork.settings')->get($this->url->getModule(), 'zoom_level_widget', 13));
        $this->form->addText('width_widget', $this->get('fork.settings')->get($this->url->getModule(), 'width_widget'));
        $this->form->addText('height_widget', $this->get('fork.settings')->get($this->url->getModule(), 'height_widget'));
        $this->form->addDropdown(
            'map_type_widget',
            [
                'ROADMAP' => BL::lbl('Roadmap', $this->getModule()),
                'SATELLITE' => BL::lbl('Satellite', $this->getModule()),
                'HYBRID' => BL::lbl('Hybrid', $this->getModule()),
                'TERRAIN' => BL::lbl('Terrain', $this->getModule()),
                'STREET_VIEW' => BL::lbl('StreetView', $this->getModule()),
            ],
            $this->get('fork.settings')->get(
                $this->url->getModule(),
                'map_type_widget',
                'roadmap'
            )
        );
    }

    protected function parse(): void
    {
        parent::parse();
        $this->template->assign('godUser', BackendAuthentication::getUser()->isGod());
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            if ($this->form->isCorrect()) {
                // set the base values
                $width = (int) $this->form->getField('width_widget')->getValue();
                $height = (int) $this->form->getField('height_widget')->getValue();

                if ($width > 800) {
                    $width = 800;
                } elseif ($width < 300) {
                    $width = $this->get('fork.settings')->get('Location', 'width_widget');
                }
                if ($height < 150) {
                    $height = $this->get('fork.settings')->get('Location', 'height_widget');
                }

                // set our settings (widgets)
                $this->get('fork.settings')->set($this->url->getModule(), 'zoom_level_widget', (string) $this->form->getField('zoom_level_widget')->getValue());
                $this->get('fork.settings')->set($this->url->getModule(), 'width_widget', $width);
                $this->get('fork.settings')->set($this->url->getModule(), 'height_widget', $height);
                $this->get('fork.settings')->set($this->url->getModule(), 'map_type_widget', (string) $this->form->getField('map_type_widget')->getValue());

                // redirect to the settings page
                $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved');
            }
        }
    }
}
