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
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the SEO-action, it will display a form to set SEO settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Seo extends BackendBaseActionIndex
{
    /**
     * The form instance
     *
     * @var    BackendForm
     */
    private $frm;

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
        $this->frm = new BackendForm('settingsSeo');
        $this->frm->addCheckbox('seo_noodp', $this->get('fork.settings')->get('Core', 'seo_noodp', false));
        $this->frm->addCheckbox('seo_noydir', $this->get('fork.settings')->get('Core', 'seo_noydir', false));
        $this->frm->addCheckbox(
            'seo_nofollow_in_comments',
            $this->get('fork.settings')->get('Core', 'seo_nofollow_in_comments', false)
        );
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        $this->frm->parse($this->tpl);
    }

    /**
     * Validates the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // no errors ?
            if ($this->frm->isCorrect()) {
                // smtp settings
                $this->get('fork.settings')->set('Core', 'seo_noodp', $this->frm->getField('seo_noodp')->getValue());
                $this->get('fork.settings')->set('Core', 'seo_noydir', $this->frm->getField('seo_noydir')->getValue());
                $this->get('fork.settings')->set(
                    'Core',
                    'seo_nofollow_in_comments',
                    $this->frm->getField('seo_nofollow_in_comments')->getValue()
                );

                // assign report
                $this->tpl->assign('report', true);
                $this->tpl->assign('reportMessage', BL::msg('Saved'));
            }
        }
    }
}
