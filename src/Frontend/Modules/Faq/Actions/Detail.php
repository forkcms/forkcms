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
    private $frm;

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

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        // hide contentTitle, in the template the title is wrapped with an inverse-option
        $this->tpl->assignGlobal('hideContentTitle', true);

        $this->loadTemplate();
        $this->getData();
        $this->updateStatistics();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // validate incoming parameters
        if ($this->URL->getParameter(1) === null) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // get by URL
        $this->record = FrontendFaqModel::get($this->URL->getParameter(1));

        // anything found?
        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        // overwrite URLs
        $this->record['category_full_url'] = FrontendNavigation::getURLForBlock('Faq', 'Category') .
                                             '/' . $this->record['category_url'];
        $this->record['full_url'] = FrontendNavigation::getURLForBlock('Faq', 'Detail') . '/' . $this->record['url'];

        // get tags
        $this->record['tags'] = FrontendTagsModel::getForItem('Faq', $this->record['id']);

        // get settings
        $this->settings = $this->get('fork.settings')->getForModule('Faq');

        // reset allow comments
        if (!$this->settings['allow_feedback']) {
            $this->record['allow_feedback'] = false;
        }

        // ge status
        $this->status = $this->URL->getParameter(2);
        if ($this->status == FL::getAction('Success')) {
            $this->status = 'success';
        }
        if ($this->status == FL::getAction('Spam')) {
            $this->status = 'spam';
        }
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('feedback');
        $this->frm->addHidden('question_id', $this->record['id']);
        $this->frm->addTextarea('message');
        $this->frm->addRadiobutton(
            'useful',
            array(
                 array('label' => FL::lbl('Yes'), 'value' => 'Y'),
                 array('label' => FL::lbl('No'), 'value' => 'N'),
            )
        );
    }

    /**
     * Parse the data into the template
     */
    private function parse()
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
        $this->tpl->assign('item', $this->record);

        // assign items in the same category and related items
        $this->tpl->assign(
            'inSameCategory',
            FrontendFaqModel::getAllForCategory(
                $this->record['category_id'],
                $this->settings['related_num_items'],
                $this->record['id']
            )
        );
        $this->tpl->assign(
            'related',
            FrontendFaqModel::getRelated($this->record['id'], $this->settings['related_num_items'])
        );

        // assign settings
        $this->tpl->assign('settings', $this->settings);

        // parse the form
        if (empty($this->status)) {
            $this->frm->parse($this->tpl);
        }

        // parse the form status
        if (!empty($this->status)) {
            $this->tpl->assign($this->status, true);
        }
    }

    /**
     * Update the view count for this item
     */
    private function updateStatistics()
    {
        // view has been counted
        if (\SpoonSession::exists('viewed_faq_' . $this->record['id'])) {
            return;
        }

        // update view count
        FrontendFaqModel::increaseViewCount($this->record['id']);

        // save in session so we know this view has been counted
        \SpoonSession::set('viewed_faq_' . $this->record['id'], true);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        $feedbackAllowed = (isset($this->settings['allow_feedback']) && $this->settings['allow_feedback']);
        if (!$feedbackAllowed) {
            return false;
        }

        if ($this->frm->isSubmitted()) {
            // reformat data
            $useful = ($this->frm->getField('useful')->getValue() == 'Y');

            // the form has been sent
            $this->tpl->assign('hideFeedbackNoInfo', $useful);

            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate required fields
            if (!$useful) {
                $this->frm->getField('message')->isFilled(FL::err('FeedbackIsRequired'));
            }

            if ($this->frm->isCorrect()) {
                // reformat data
                $text = $this->frm->getField('message')->getValue();

                // get feedback in session
                $previousFeedback = (\SpoonSession::exists('faq_feedback_' . $this->record['id']) ? \SpoonSession::get(
                    'faq_feedback_' . $this->record['id']
                ) : null);

                // update counters
                FrontendFaqModel::updateFeedback($this->record['id'], $useful, $previousFeedback);

                // save feedback in session
                \SpoonSession::set('faq_feedback_' . $this->record['id'], $useful);

                // answer is yes so there's no feedback
                if (!$useful) {
                    // get module setting
                    $spamFilterEnabled = (isset($this->settings['spamfilter']) && $this->settings['spamfilter']);

                    // build array
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
                            ->setFrom(array($from['email'] => $from['name']))
                            ->setTo(array($to['email'] => $to['name']))
                            ->setReplyTo(array($replyTo['email'] => $replyTo['name']))
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
            $this->tpl->assign('hideFeedbackNoInfo', true);
        }
    }
}
