<?php

namespace Backend\Modules\Settings\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;

/**
 * This is the SEO-action, it will display a form to set SEO settings
 */
class Seo extends BackendBaseActionIndex
{
    /**
     * The form instance
     *
     * @var BackendForm
     */
    private $form;

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
        $this->form = new BackendForm('settingsSeo');
        $this->form->addCheckbox('seo_noodp', $this->get('fork.settings')->get('Core', 'seo_noodp', false));
        $this->form->addCheckbox('seo_noydir', $this->get('fork.settings')->get('Core', 'seo_noydir', false));
        $this->form->addCheckbox(
            'seo_nofollow_in_comments',
            $this->get('fork.settings')->get('Core', 'seo_nofollow_in_comments', false)
        );
    }

    protected function parse(): void
    {
        parent::parse();

        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // no errors ?
            if ($this->form->isCorrect()) {
                // smtp settings
                $this->get('fork.settings')->set('Core', 'seo_noodp', $this->form->getField('seo_noodp')->getValue());
                $this->get('fork.settings')->set('Core', 'seo_noydir', $this->form->getField('seo_noydir')->getValue());
                $this->get('fork.settings')->set(
                    'Core',
                    'seo_nofollow_in_comments',
                    $this->form->getField('seo_nofollow_in_comments')->getValue()
                );

                // assign report
                $this->template->assign('report', true);
                $this->template->assign('reportMessage', BL::msg('Saved'));
            }
        }
    }
}
