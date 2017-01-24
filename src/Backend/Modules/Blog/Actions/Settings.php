<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;

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
        $this->isGod = BackendAuthentication::getUser()->isGod();

        $this->frm = new BackendForm('settings');

        // add fields for pagination
        $this->frm->addDropdown(
            'overview_number_of_items',
            array_combine(range(1, 30), range(1, 30)),
            $this->get('fork.settings')->get($this->URL->getModule(), 'overview_num_items', 10)
        );
        $this->frm->addDropdown(
            'recent_articles_full_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('fork.settings')->get($this->URL->getModule(), 'recent_articles_full_num_items', 5)
        );
        $this->frm->addDropdown(
            'recent_articles_list_number_of_items',
            array_combine(range(1, 10), range(1, 10)),
            $this->get('fork.settings')->get($this->URL->getModule(), 'recent_articles_list_num_items', 5)
        );

        // add fields for spam
        $this->frm->addCheckbox('spamfilter', $this->get('fork.settings')->get($this->URL->getModule(), 'spamfilter', false));

        // no Akismet-key, so we can't enable spam-filter
        if ($this->get('fork.settings')->get('Core', 'akismet_key') == '') {
            $this->frm->getField('spamfilter')->setAttribute('disabled', 'disabled');
            $this->tpl->assign('noAkismetKey', true);
        }

        // add fields for comments
        $this->frm->addCheckbox('allow_comments', $this->get('fork.settings')->get($this->URL->getModule(), 'allow_comments', false));
        $this->frm->addCheckbox('moderation', $this->get('fork.settings')->get($this->URL->getModule(), 'moderation', false));

        // add fields for notifications
        $this->frm->addCheckbox(
            'notify_by_email_on_new_comment_to_moderate',
            $this->get('fork.settings')->get($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', false)
        );
        $this->frm->addCheckbox(
            'notify_by_email_on_new_comment',
            $this->get('fork.settings')->get($this->URL->getModule(), 'notify_by_email_on_new_comment', false)
        );

        // add fields for RSS
        $this->frm->addCheckbox('rss_meta', $this->get('fork.settings')->get($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), true));
        $this->frm->addText('rss_title', $this->get('fork.settings')->get($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage()));
        $this->frm->addTextarea('rss_description', $this->get('fork.settings')->get($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage()));

        // god user?
        if ($this->isGod) {
            $this->frm->addCheckbox('show_image_form', $this->get('fork.settings')->get($this->URL->getModule(), 'show_image_form', true));
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // parse additional variables
        $this->tpl->assign('commentsRSSURL', SITE_URL . BackendModel::getURLForBlock($this->URL->getModule(), 'comments_rss'));
        $this->tpl->assign('isGod', $this->isGod);
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            // validation
            $this->frm->getField('rss_title')->isFilled(BL::err('FieldIsRequired'));

            if ($this->frm->isCorrect()) {
                // set our settings
                $this->get('fork.settings')->set($this->URL->getModule(), 'overview_num_items', (int) $this->frm->getField('overview_number_of_items')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'recent_articles_full_num_items', (int) $this->frm->getField('recent_articles_full_number_of_items')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'recent_articles_list_num_items', (int) $this->frm->getField('recent_articles_list_number_of_items')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'allow_comments', (bool) $this->frm->getField('allow_comments')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'moderation', (bool) $this->frm->getField('moderation')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', (bool) $this->frm->getField('notify_by_email_on_new_comment_to_moderate')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'notify_by_email_on_new_comment', (bool) $this->frm->getField('notify_by_email_on_new_comment')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), $this->frm->getField('rss_meta')->getValue());
                if ($this->isGod) {
                    $this->get('fork.settings')->set($this->URL->getModule(), 'show_image_form', (bool) $this->frm->getField('show_image_form')->getChecked());
                }
                if ($this->get('fork.settings')->get('Core', 'akismet_key') === null) {
                    $this->get('fork.settings')->set($this->URL->getModule(), 'spamfilter', false);
                }

                // redirect to the settings page
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }
}
