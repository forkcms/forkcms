<?php

namespace Frontend\Modules\Faq\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Mailer\Message;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;

/**
 * This is a widget with the form to ask a question
 */
class AskOwnQuestion extends FrontendBaseWidget
{
    /**
     * Form instance
     *
     * @var FrontendForm
     */
    private $form;

    /**
     * The form status
     *
     * @var string
     */
    private $status;

    public function execute(): void
    {
        parent::execute();

        $this->loadTemplate();

        if (!$this->get('fork.settings')->get('Faq', 'allow_own_question', false)) {
            return;
        }

        $this->buildForm();
        $this->validateForm();
        $this->parse();
    }

    private function buildForm(): void
    {
        // create form
        $this->form = new FrontendForm('own_question', '#' . FL::getAction('OwnQuestion'));
        $this->form->addText('name')->setAttributes(['required' => null]);
        $this->form->addText('email')->setAttributes(['required' => null, 'type' => 'email']);
        $this->form->addTextarea('message')->setAttributes(['required' => null]);
    }

    private function parse(): void
    {
        // parse the form or a status
        if (empty($this->status)) {
            $this->form->parse($this->template);
        } else {
            $this->template->assign($this->status, true);
        }

        // parse an option so the stuff can be shown
        $this->template->assign('widgetFaqOwnQuestion', true);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            $this->form->cleanupFields();

            // validate required fields
            $this->form->getField('name')->isFilled(FL::err('NameIsRequired'));
            $this->form->getField('email')->isEmail(FL::err('EmailIsInvalid'));
            $this->form->getField('message')->isFilled(FL::err('QuestionIsRequired'));

            if ($this->form->isCorrect()) {
                $spamFilterEnabled = $this->get('fork.settings')->get('Faq', 'spamfilter');
                $variables = [];
                $variables['sentOn'] = time();
                $variables['name'] = $this->form->getField('name')->getValue();
                $variables['email'] = $this->form->getField('email')->getValue();
                $variables['message'] = $this->form->getField('message')->getValue();

                if ($spamFilterEnabled) {
                    // if the comment is spam alter the comment status so it will appear in the spam queue
                    if (FrontendModel::isSpam(
                        $variables['message'],
                        SITE_URL . FrontendNavigation::getUrlForBlock('Faq'),
                        $variables['name'],
                        $variables['email']
                    )
                    ) {
                        $this->status = 'errorSpam';

                        return;
                    }
                }

                $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                $to = $this->get('fork.settings')->get('Core', 'mailer_to');
                $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                $message = Message::newInstance(sprintf(FL::getMessage('FaqOwnQuestionSubject'), $variables['name']))
                    ->setFrom([$from['email'] => $from['name']])
                    ->setTo([$to['email'] => $to['name']])
                    ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                    ->parseHtml(
                        '/Faq/Layout/Templates/Mails/OwnQuestion.html.twig',
                        $variables,
                        true
                    )
                ;
                $this->get('mailer')->send($message);
                $this->status = 'success';
            }
        }
    }
}
