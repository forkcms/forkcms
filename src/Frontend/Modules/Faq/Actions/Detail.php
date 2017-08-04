<?php

namespace Frontend\Modules\Faq\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Mailer\Message;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is the detail-action
 */
class Detail extends FrontendBaseBlock
{
    /**
     * Form instance
     *
     * @var FrontendForm
     */
    private $form;

    /**
     * The faq
     *
     * @var array
     */
    private $record;

    /**
     * The settings
     *
     * @var array
     */
    private $settings;

    /**
     * The status of the form
     *
     * @var string
     */
    private $status;

    public function execute(): void
    {
        parent::execute();

        // hide contentTitle, in the template the title is wrapped with an inverse-option
        $this->template->assignGlobal('hideContentTitle', true);

        $this->loadTemplate();
        $this->getData();
        $this->updateStatistics();
        $this->buildForm();
        $this->validateForm();
        $this->parse();
    }

    private function getData(): void
    {
        // validate incoming parameters
        if ($this->url->getParameter(1) === null) {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // get by URL
        $this->record = FrontendFaqModel::get($this->url->getParameter(1));

        // anything found?
        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getUrl(404));
        }

        // overwrite URLs
        $this->record['category_full_url'] = FrontendNavigation::getUrlForBlock('Faq', 'Category') .
                                             '/' . $this->record['category_url'];
        $this->record['full_url'] = FrontendNavigation::getUrlForBlock('Faq', 'Detail') . '/' . $this->record['url'];

        // get tags
        $this->record['tags'] = FrontendTagsModel::getForItem('Faq', $this->record['id']);

        // get settings
        $this->settings = $this->get('fork.settings')->getForModule('Faq');

        // reset allow comments
        if (!$this->settings['allow_feedback']) {
            $this->record['allow_feedback'] = false;
        }

        // ge status
        $this->status = $this->url->getParameter(2);
        if ($this->status == FL::getAction('Success')) {
            $this->status = 'success';
        }
        if ($this->status == FL::getAction('Spam')) {
            $this->status = 'spam';
        }
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('feedback');
        $this->form->addHidden('question_id', $this->record['id']);
        $this->form->addTextarea('message')->setAttributes(
            [
                'data-role' => 'fork-feedback-improve-message',
            ]
        );
        $this->form->addRadiobutton(
            'useful',
            [
                 [
                     'label' => FL::lbl('Yes'),
                     'value' => 1,
                     'attributes' => [
                         'data-role' => 'fork-feedback-useful',
                         'data-response' => 'yes',
                     ],
                 ],
                 [
                     'label' => FL::lbl('No'),
                     'value' => 0,
                     'attributes' => [
                         'data-role' => 'fork-feedback-useful',
                         'data-response' => 'no',
                     ],
                 ],
            ]
        );
    }

    private function parse(): void
    {
        // add to breadcrumb
        if ($this->settings['allow_multiple_categories']) {
            $this->breadcrumb->addElement(
                $this->record['category_title'],
                $this->record['category_full_url']
            );
        }
        $this->breadcrumb->addElement($this->record['question']);

        // set meta
        if ($this->settings['allow_multiple_categories']) {
            $this->header->setPageTitle($this->record['category_title']);
        }
        $this->header->setPageTitle($this->record['question']);

        // assign article
        $this->template->assign('item', $this->record);

        // assign items in the same category and related items
        $this->template->assign(
            'inSameCategory',
            FrontendFaqModel::getAllForCategory(
                $this->record['category_id'],
                $this->settings['related_num_items'],
                $this->record['id']
            )
        );
        $this->template->assign(
            'related',
            FrontendFaqModel::getRelated($this->record['id'], $this->settings['related_num_items'])
        );

        // assign settings
        $this->template->assign('settings', $this->settings);

        // parse the form
        if (empty($this->status)) {
            $this->form->parse($this->template);
        }

        // parse the form status
        if (!empty($this->status)) {
            $this->template->assign($this->status, true);
        }
    }

    /**
     * Update the view count for this item
     */
    private function updateStatistics(): void
    {
        // view has been counted
        if (FrontendModel::getSession()->has('viewed_faq_' . $this->record['id'])) {
            return;
        }

        // update view count
        FrontendFaqModel::increaseViewCount($this->record['id']);

        // save in session so we know this view has been counted
        FrontendModel::getSession()->set('viewed_faq_' . $this->record['id'], true);
    }

    private function validateForm(): void
    {
        $feedbackAllowed = (isset($this->settings['allow_feedback']) && $this->settings['allow_feedback']);
        if (!$feedbackAllowed) {
            return;
        }

        if ($this->form->isSubmitted()) {
            // reformat data
            $useful = $this->form->getField('useful')->isChecked();

            // the form has been sent
            $this->template->assign('hideFeedbackNoInfo', $useful);

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->form->cleanupFields();

            // validate required fields
            if (!$useful) {
                $this->form->getField('message')->isFilled(FL::err('FeedbackIsRequired'));
            }

            if ($this->form->isCorrect()) {
                // reformat data
                $text = $this->form->getField('message')->getValue();

                // get feedback in session
                $previousFeedback = FrontendModel::getSession()->get('faq_feedback_' . $this->record['id']);

                // update counters
                FrontendFaqModel::updateFeedback($this->record['id'], $useful, $previousFeedback);

                // save feedback in session
                FrontendModel::getSession()->set('faq_feedback_' . $this->record['id'], $useful);

                // answer is yes so there's no feedback
                if (!$useful) {
                    // get module setting
                    $spamFilterEnabled = (isset($this->settings['spamfilter']) && $this->settings['spamfilter']);

                    // build array
                    $variables = [];
                    $variables['question_id'] = $this->record['id'];
                    $variables['sentOn'] = time();
                    $variables['text'] = $text;

                    // should we check if the item is spam
                    if ($spamFilterEnabled) {
                        // the comment is spam
                        if (FrontendModel::isSpam($text, $variables['question_link'])) {
                            // set the status to spam
                            $this->redirect($this->record['full_url'] . '/' . FL::getAction('Spam'));
                        }
                    }

                    // save the feedback
                    FrontendFaqModel::saveFeedback($variables);

                    // send email on new feedback?
                    if ($this->get('fork.settings')->get('Faq', 'send_email_on_new_feedback')) {
                        // add the question
                        $variables['question'] = $this->record['question'];

                        $to = $this->get('fork.settings')->get('Core', 'mailer_to');
                        $from = $this->get('fork.settings')->get('Core', 'mailer_from');
                        $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
                        $message = Message::newInstance(
                            sprintf(FL::getMessage('FaqFeedbackSubject'), $this->record['question'])
                        )
                            ->setFrom([$from['email'] => $from['name']])
                            ->setTo([$to['email'] => $to['name']])
                            ->setReplyTo([$replyTo['email'] => $replyTo['name']])
                            ->parseHtml(
                                '/Faq/Layout/Templates/Mails/Feedback.html.twig',
                                $variables,
                                true
                            )
                        ;
                        $this->get('mailer')->send($message);
                    }
                }

                // save status
                $this->redirect($this->record['full_url'] . '/' . FL::getAction('Success'));
            }
        } else {
            // form hasn't been sent
            $this->template->assign('hideFeedbackNoInfo', true);
        }
    }
}
