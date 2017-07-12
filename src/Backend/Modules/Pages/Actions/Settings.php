<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the settings-action, it will display a form to set general pages settings
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
        // init settings form
        $this->form = new BackendForm('settings');

        // add fields for meta navigation
        $this->form->addCheckbox(
            'meta_navigation',
            $this->get('fork.settings')->get($this->getModule(), 'meta_navigation', false)
        );
    }

    private function validateForm(): void
    {
        // form is submitted
        if ($this->form->isSubmitted()) {
            // form is validated
            if ($this->form->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'meta_navigation',
                    (bool) $this->form->getField('meta_navigation')->getValue()
                );

                // redirect to the settings page
                $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved');
            }
        }
    }
}
