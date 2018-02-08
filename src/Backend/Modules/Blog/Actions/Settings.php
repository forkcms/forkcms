<?php

namespace Backend\Modules\Blog\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use App\Component\Locale\BackendLanguage;

/**
 * This is the settings-action, it will display a form to set general blog settings
 */
class Settings extends BackendBaseActionEdit
{
    /**
     * Is the user a god user?
     *
     * @var bool
     */
    protected $isGod = false;

    public function execute(): void
    {
        parent::execute();
        $this->buildForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function buildForm(): void
    {
        $this->isGod = BackendAuthentication::getUser()->isGod();

        $this->form = new BackendForm('settings');

        // add fields for pagination
        $this->form->addDropdown(
            'overview_number_of_items',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('forkcms.settings')->get($this->url->getModule(), 'overview_num_items', 10)
        );
        $this->form->addDropdown(
            'recent_articles_full_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('forkcms.settings')->get($this->url->getModule(), 'recent_articles_full_num_items', 5)
        );
        $this->form->addDropdown(
            'recent_articles_list_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('forkcms.settings')->get($this->url->getModule(), 'recent_articles_list_num_items', 5)
        );

        // add fields for spam
        $this->form->addCheckbox('spamfilter', $this->get('forkcms.settings')->get($this->url->getModule(), 'spamfilter', false));

        // no Akismet-key, so we can't enable spam-filter
        if ($this->get('forkcms.settings')->get('Core', 'akismet_key') == '') {
            $this->form->getField('spamfilter')->setAttribute('disabled', 'disabled');
            $this->template->assign('noAkismetKey', true);
        }

        // add fields for comments
        $this->form->addCheckbox('allow_comments', $this->get('forkcms.settings')->get($this->url->getModule(), 'allow_comments', false));
        $this->form->addCheckbox('moderation', $this->get('forkcms.settings')->get($this->url->getModule(), 'moderation', false));

        // add fields for notifications
        $this->form->addCheckbox(
            'notify_by_email_on_new_comment_to_moderate',
            $this->get('forkcms.settings')->get($this->url->getModule(), 'notify_by_email_on_new_comment_to_moderate', false)
        );
        $this->form->addCheckbox(
            'notify_by_email_on_new_comment',
            $this->get('forkcms.settings')->get($this->url->getModule(), 'notify_by_email_on_new_comment', false)
        );

        // add fields for RSS
        $this->form->addCheckbox('rss_meta', $this->get('forkcms.settings')->get($this->url->getModule(), 'rss_meta_' . BackendLanguage::getWorkingLanguage(), true));
        $this->form->addText('rss_title', $this->get('forkcms.settings')->get($this->url->getModule(), 'rss_title_' . BackendLanguage::getWorkingLanguage()));
        $this->form->addTextarea('rss_description', $this->get('forkcms.settings')->get($this->url->getModule(), 'rss_description_' . BackendLanguage::getWorkingLanguage()));

        // god user?
        if ($this->isGod) {
            $this->form->addCheckbox('show_image_form', $this->get('forkcms.settings')->get($this->url->getModule(), 'show_image_form', true));
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // parse additional variables
        $this->template->assign('commentsRSSURL', SITE_URL . BackendModel::getUrlForBlock($this->url->getModule(), 'comments_rss'));
        $this->template->assign('isGod', $this->isGod);
    }

    private function validateForm(): void
    {
        if ($this->form->isSubmitted()) {
            // validation
            $this->form->getField('rss_title')->isFilled(BackendLanguage::err('FieldIsRequired'));

            if ($this->form->isCorrect()) {
                // set our settings
                $this->get('forkcms.settings')->set($this->url->getModule(), 'overview_num_items', (int) $this->form->getField('overview_number_of_items')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'recent_articles_full_num_items', (int) $this->form->getField('recent_articles_full_number_of_items')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'recent_articles_list_num_items', (int) $this->form->getField('recent_articles_list_number_of_items')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'spamfilter', (bool) $this->form->getField('spamfilter')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'allow_comments', (bool) $this->form->getField('allow_comments')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'moderation', (bool) $this->form->getField('moderation')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'notify_by_email_on_new_comment_to_moderate', (bool) $this->form->getField('notify_by_email_on_new_comment_to_moderate')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'notify_by_email_on_new_comment', (bool) $this->form->getField('notify_by_email_on_new_comment')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'rss_title_' . BackendLanguage::getWorkingLanguage(), $this->form->getField('rss_title')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'rss_description_' . BackendLanguage::getWorkingLanguage(), $this->form->getField('rss_description')->getValue());
                $this->get('forkcms.settings')->set($this->url->getModule(), 'rss_meta_' . BackendLanguage::getWorkingLanguage(), $this->form->getField('rss_meta')->getValue());
                if ($this->isGod) {
                    $this->get('forkcms.settings')->set($this->url->getModule(), 'show_image_form', (bool) $this->form->getField('show_image_form')->getChecked());
                }
                if ($this->get('forkcms.settings')->get('Core', 'akismet_key') === null) {
                    $this->get('forkcms.settings')->set($this->url->getModule(), 'spamfilter', false);
                }

                // redirect to the settings page
                $this->redirect(BackendModel::createUrlForAction('Settings') . '&report=saved');
            }
        }
    }
}
