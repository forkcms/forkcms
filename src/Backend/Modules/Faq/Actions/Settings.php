<?php

namespace Backend\Modules\Faq\Actions;

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
 * This is the settings-action, it will display a form to set general faq settings
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
        $this->frm = new BackendForm('settings');
        $this->frm->addDropdown(
            'overview_number_of_items_per_category',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('fork.settings')->get($this->url->getModule(), 'overview_num_items_per_category', 10)
        );
        $this->frm->addDropdown(
            'most_read_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('fork.settings')->get($this->url->getModule(), 'most_read_num_items', 10)
        );
        $this->frm->addDropdown(
            'related_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('fork.settings')->get($this->url->getModule(), 'related_num_items', 3)
        );
        $this->frm->addCheckbox(
            'allow_multiple_categories',
            $this->get('fork.settings')->get($this->url->getModule(), 'allow_multiple_categories', false)
        );
        $this->frm->addCheckbox(
            'spamfilter',
            $this->get('fork.settings')->get($this->url->getModule(), 'spamfilter', false)
        );
        $this->frm->addCheckbox(
            'allow_feedback',
            $this->get('fork.settings')->get($this->url->getModule(), 'allow_feedback', false)
        );
        $this->frm->addCheckbox(
            'allow_own_question',
            $this->get('fork.settings')->get($this->url->getModule(), 'allow_own_question', false)
        );
        $this->frm->addCheckbox(
            'send_email_on_new_feedback',
            $this->get('fork.settings')->get($this->url->getModule(), 'send_email_on_new_feedback', false)
        );

        // no Akismet-key, so we can't enable spam-filter
        if ($this->get('fork.settings')->get('Core', 'akismet_key') == '') {
            $this->frm->getField('spamfilter')->setAttribute('disabled', 'disabled');
            $this->tpl->assign('noAkismetKey', true);
        }
    }

    private function validateForm(): void
    {
        if ($this->frm->isSubmitted()) {
            if ($this->frm->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'overview_num_items_per_category',
                    (int) $this->frm->getField('overview_number_of_items_per_category')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'most_read_num_items',
                    (int) $this->frm->getField('most_read_number_of_items')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'related_num_items',
                    (int) $this->frm->getField('related_number_of_items')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'allow_multiple_categories',
                    (bool) $this->frm->getField('allow_multiple_categories')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'spamfilter',
                    (bool) $this->frm->getField('spamfilter')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'allow_feedback',
                    (bool) $this->frm->getField('allow_feedback')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'allow_own_question',
                    (bool) $this->frm->getField('allow_own_question')->getValue()
                );
                $this->get('fork.settings')->set(
                    $this->url->getModule(),
                    'send_email_on_new_feedback',
                    (bool) $this->frm->getField('send_email_on_new_feedback')->getValue()
                );
                if ($this->get('fork.settings')->get('Core', 'akismet_key') === null) {
                    $this->get('fork.settings')->set($this->url->getModule(), 'spamfilter', false);
                }

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }
}
