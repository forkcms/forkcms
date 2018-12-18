<?php

namespace Frontend\Modules\Faq\Actions;

use Common\Mailer\Message;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the detail-action
 */
class Detail extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $feedbackForm;

    /**
     * @var array
     */
    private $question;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();

        // hide contentTitle, in the template the title is wrapped with an inverse-option
        $this->template->assignGlobal('hideContentTitle', true);

        $this->question = $this->getQuestion();
        $this->increaseViewCount();
        $this->buildForm();
        $this->handleForm();
        $this->parse();
    }

    /**
     * Shortcut to get a module settings
     *
     * @param string $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function getSetting(string $name, $default = null)
    {
        return $this->get('fork.settings')->get($this->getModule(), $name, $default);
    }

    private function getQuestion(): array
    {
        if ($this->url->getParameter(1) === null) {
            throw new NotFoundHttpException();
        }

        $question = FrontendFaqModel::get($this->url->getParameter(1));

        if (empty($question)) {
            throw new NotFoundHttpException();
        }

        $baseCategoryUrl = FrontendNavigation::getUrlForBlock($this->getModule(), 'Category');
        $question['category_full_url'] = $baseCategoryUrl . '/' . $question['category_url'];
        $baseQuestionUrl = FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction());
        $question['full_url'] = $baseQuestionUrl . '/' . $question['url'];
        $question['tags'] = FrontendTagsModel::getForItem($this->getModule(), $question['id']);
        $question['allow_feedback'] = $this->isFeedbackAllowed();

        return $question;
    }

    private function isFeedbackAllowed(): bool
    {
        return $this->getSetting('allow_feedback', false);
    }

    private function hasStatus(): bool
    {
        return $this->getStatus() !== null;
    }

    private function getStatus(): ?string
    {
        switch ($this->url->getParameter(2)) {
            case FL::getAction('Success'):
                return 'success';
            case FL::getAction('Spam'):
                return 'spam';
            default:
                return null;
        }
    }

    private function buildForm(): void
    {
        $this->feedbackForm = new FrontendForm('feedback');
        $this->feedbackForm->addHidden('question_id', $this->question['id']);
        $this->feedbackForm->addTextarea('message')->setAttributes(
            [
                'data-role' => 'fork-feedback-improve-message',
            ]
        );
        $this->feedbackForm->addRadiobutton(
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

    private function isMultipleCategoriesAllowed(): bool
    {
        return $this->getSetting('allow_multiple_categories', false);
    }

    private function setPageTitle(): void
    {
        if ($this->isMultipleCategoriesAllowed()) {
            $this->header->setPageTitle($this->question['category_title']);
        }

        $this->header->setPageTitle($this->question['question']);
    }

    private function addBreadcrumbs(): void
    {
        if ($this->isMultipleCategoriesAllowed()) {
            $this->breadcrumb->addElement($this->question['category_title'], $this->question['category_full_url']);
        }

        $this->breadcrumb->addElement($this->question['question']);
    }

    private function parse(): void
    {
        $this->addBreadcrumbs();
        $this->setPageTitle();

        $this->template->assign('item', $this->question);
        $this->template->assign('inSameCategory', $this->getRelatedQuestionsFromTheSameCategory());
        $this->template->assign('related', $this->getRelatedQuestions());
        $this->template->assign('settings', $this->get('fork.settings')->getForModule($this->getModule()));

        if ($this->hasStatus()) {
            $this->template->assign($this->getStatus(), true);

            return;
        }

        $this->template->assign('hideFeedbackNoInfo', $this->isFeedbackNoInfoHidden());
        $this->feedbackForm->parse($this->template);
    }

    private function isFeedbackNoInfoHidden(): bool
    {
        if ($this->feedbackForm->isSubmitted()) {
            return (bool) $this->feedbackForm->getField('useful')->getValue();
        }

        return true;
    }

    private function increaseViewCount(): void
    {
        // already viewed this question in the current session
        if (FrontendModel::getSession()->has('viewed_faq_' . $this->question['id'])) {
            return;
        }

        FrontendFaqModel::increaseViewCount($this->question['id']);

        // save that this question has been viewed for the current session to prevent counting a page refresh as 2 views
        FrontendModel::getSession()->set('viewed_faq_' . $this->question['id'], true);
    }

    private function validateForm(): bool
    {
        $this->feedbackForm->cleanupFields();

        // if the visitor didn't check whether the answer was useful the visitor should submit feedback
        if (!$this->isVisitorSayingTheAnswerWasUseful()) {
            $this->feedbackForm->getField('message')->isFilled(FL::err('FeedbackIsRequired'));
        }

        return $this->feedbackForm->isCorrect();
    }

    private function isVisitorSayingTheAnswerWasUseful(): bool
    {
        return (bool) $this->feedbackForm->getField('useful')->getValue();
    }

    private function handleForm(): void
    {
        if (!$this->isFeedbackAllowed() || !$this->feedbackForm->isSubmitted() || !$this->validateForm()) {
            return;
        }

        $this->updateUsefulnessCounters();

        if (!$this->isVisitorSayingTheAnswerWasUseful()) {
            $this->saveUserFeedback();
        }

        $this->redirect($this->question['full_url'] . '/' . FL::getAction('Success'));
    }

    private function isSpamFilterEnabled(): bool
    {
        return $this->getSetting('spamfilter', false);
    }

    private function isNotificationMailForNewFeedbackEnabled(): bool
    {
        return $this->getSetting('send_email_on_new_feedback', false);
    }

    private function saveUserFeedback(): void
    {
        $feedback = [
            'question_id' => $this->question['id'],
            'sentOn' => time(),
            'text' => $this->feedbackForm->getField('message')->getValue(),
        ];

        if ($this->isSpamFilterEnabled() && FrontendModel::isSpam($feedback['text'], $feedback['question_link'])) {
            // set the status to spam
            $this->redirect($this->question['full_url'] . '/' . FL::getAction('Spam'));
        }

        // save the feedback
        FrontendFaqModel::saveFeedback($feedback);

        // send email on new feedback?
        if ($this->isNotificationMailForNewFeedbackEnabled()) {
            $this->sendNotificationMail($feedback);
        }
    }

    private function getRelatedQuestionsFromTheSameCategory(): array
    {
        return FrontendFaqModel::getAllForCategory(
            $this->question['category_id'],
            $this->getSetting('related_num_items', 5),
            $this->question['id']
        );
    }

    private function getRelatedQuestions(): array
    {
        return FrontendFaqModel::getRelated($this->question['id'], $this->getSetting('related_num_items', 5));
    }

    private function getPreviousUsefulnessAnswerFromSession(): ?bool
    {
        return FrontendModel::getSession()->get('faq_feedback_' . $this->question['id']);
    }

    private function updateUsefulnessCounters(): void
    {
        FrontendFaqModel::updateFeedback(
            $this->question['id'],
            $this->isVisitorSayingTheAnswerWasUseful(),
            $this->getPreviousUsefulnessAnswerFromSession()
        );

        FrontendModel::getSession()->set(
            'faq_feedback_' . $this->question['id'],
            $this->isVisitorSayingTheAnswerWasUseful()
        );
    }

    private function sendNotificationMail(array $feedback): void
    {
        $feedback['question'] = $this->question['question'];

        $to = $this->get('fork.settings')->get('Core', 'mailer_to');
        $from = $this->get('fork.settings')->get('Core', 'mailer_from');
        $replyTo = $this->get('fork.settings')->get('Core', 'mailer_reply_to');
        $message = Message::newInstance(
            sprintf(FL::getMessage('FaqFeedbackSubject'), $feedback['question'])
        )
            ->setFrom([$from['email'] => $from['name']])
            ->setTo([$to['email'] => $to['name']])
            ->setReplyTo([$replyTo['email'] => $replyTo['name']])
            ->parseHtml(
                '/Faq/Layout/Templates/Mails/Feedback.html.twig',
                $feedback,
                true
            );
        $this->get('mailer')->send($message);
    }
}
