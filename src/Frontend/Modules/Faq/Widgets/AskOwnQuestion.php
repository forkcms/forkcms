<?php

namespace Frontend\Modules\Faq\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is a widget with the form to ask a question
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class AskOwnQuestion extends FrontendBaseWidget
{
    /**
     * Form instance
     *
     * @var FrontendForm
     */
    private $frm;

    /**
     * The form status
     *
     * @var string
     */
    private $status = null;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $this->loadTemplate();

        if (!FrontendModel::getModuleSetting('Faq', 'allow_own_question', false)) {
            return;
        }

        $this->loadForm();
        $this->validateForm();
        $this->parse();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new FrontendForm('own_question', '#' . FL::getAction('OwnQuestion'));
        $this->frm->addText('name')->setAttributes(array('required' => null));
        $this->frm->addText('email')->setAttributes(array('required' => null, 'type' => 'email'));
        $this->frm->addTextarea('message')->setAttributes(array('required' => null));
    }

    /**
     * Parse
     */
    private function parse()
    {
        // parse the form or a status
        if (empty($this->status)) {
            $this->frm->parse($this->tpl);
        } else {
            $this->tpl->assign($this->status, true);
        }

        // parse an option so the stuff can be shown
        $this->tpl->assign('widgetFaqOwnQuestion', true);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate required fields
            $this->frm->getField('name')->isFilled(FL::err('NameIsRequired'));
            $this->frm->getField('email')->isEmail(FL::err('EmailIsInvalid'));
            $this->frm->getField('message')->isFilled(FL::err('QuestionIsRequired'));

            if ($this->frm->isCorrect()) {
                $spamFilterEnabled = FrontendModel::getModuleSetting('Faq', 'spamfilter');
                $variables['sentOn'] = time();
                $variables['name'] = $this->frm->getField('name')->getValue();
                $variables['email'] = $this->frm->getField('email')->getValue();
                $variables['message'] = $this->frm->getField('message')->getValue();

                if ($spamFilterEnabled) {
                    // if the comment is spam alter the comment status so it will appear in the spam queue
                    if (FrontendModel::isSpam(
                        $variables['message'],
                        SITE_URL . FrontendNavigation::getURLForBlock('Faq'),
                        $variables['name'],
                        $variables['email']
                    )
                    ) {
                        $this->status = 'errorSpam';

                        return;
                    }
                }

                $this->status = 'success';
                $this->get('mailer')->addEmail(
                    sprintf(FL::getMessage('FaqOwnQuestionSubject'), $variables['name']),
                    FRONTEND_MODULES_PATH . '/Faq/Layout/Templates/Mails/own_question.tpl',
                    $variables,
                    $variables['email'],
                    $variables['name'],
                    null, null, null, null, null, null, null, null, null, true
                );
            }
        }
    }
}
