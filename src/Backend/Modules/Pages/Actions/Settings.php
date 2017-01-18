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
        // init settings form
        $this->frm = new BackendForm('settings');

        // add fields for meta navigation
        $this->frm->addCheckbox(
            'meta_navigation',
            $this->get('fork.settings')->get($this->getModule(), 'meta_navigation', false)
        );
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        // form is submitted
        if ($this->frm->isSubmitted()) {
            // form is validated
            if ($this->frm->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->getModule(),
                    'meta_navigation',
                    (bool) $this->frm->getField('meta_navigation')->getValue()
                );

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }
}
