<?php

namespace Backend\Modules\Faq\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the faq module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Installer extends ModuleInstaller
{
    /**
     * @var	int
     */
    private $defaultCategoryId;

    /**
     * Add a category for a language
     *
     * @param string $language
     * @param string $title
     * @param string $url
     * @return int
     */
    private function addCategory($language, $title, $url)
    {
        // db
        $db = $this->getDB();

        // get sequence for widget
        $sequenceExtra = $db->getVar(
            'SELECT MAX(i.sequence) + 1
             FROM modules_extras AS i
             WHERE i.module = ?',
            array('faq')
        );

        // build array
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['extra_id'] = $this->insertExtra('Faq', 'widget', 'Faq', 'CategoryList', null, 'N', $sequenceExtra);
        $item['language'] = (string) $language;
        $item['title'] = (string) $title;
        $item['sequence'] = 1;

        // insert category
        $item['id'] = (int) $db->insert('faq_categories', $item);

        // build data for widget
        $extra['data'] = serialize(
            array(
                'id' => $item['id'],
                'extra_label' => 'Category: ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => '/private/' . $language . '/faq/edit_category?id=' . $item['id']
            )
        );

        // update widget
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($item['extra_id'], 'faq', 'widget', 'category_list')
        );

        return $item['id'];
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language
     * @return int
     */
    private function getCategory($language)
    {
        return (int) $this->getDB()->getVar(
            'SELECT id
             FROM faq_categories
             WHERE language = ?',
            array((string) $language)
        );
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $feedback = array(
            'column' => 'right',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        $this->insertDashboardWidget('Faq', 'Feedback', $feedback);
    }

    /**
     * Install the module
     */
    public function install()
    {
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        $this->addModule('Faq');

        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->makeSearchable('Faq');
        $this->setModuleRights(1, 'Faq');

        $this->setActionRights(1, 'Faq', 'Index');
        $this->setActionRights(1, 'Faq', 'Add');
        $this->setActionRights(1, 'Faq', 'Edit');
        $this->setActionRights(1, 'Faq', 'Delete');
        $this->setActionRights(1, 'Faq', 'Sequence');
        $this->setActionRights(1, 'Faq', 'Categories');
        $this->setActionRights(1, 'Faq', 'AddCategory');
        $this->setActionRights(1, 'Faq', 'EditCategory');
        $this->setActionRights(1, 'Faq', 'DeleteCategory');
        $this->setActionRights(1, 'Faq', 'SequenceQuestions');
        $this->setActionRights(1, 'Faq', 'DeleteFeedback');
        $this->setActionRights(1, 'Faq', 'Settings');

        $faqId = $this->insertExtra('Faq', 'block', 'Faq');

        // Register widgets
        // Category faq widgets will be added on the fly
        $this->insertExtra('Faq', 'widget', 'MostReadQuestions', 'MostReadQuestions');
        $this->insertExtra('Faq', 'widget', 'AskOwnQuestion', 'AskOwnQuestion');

        $this->setSetting('Faq', 'overview_num_items_per_category', 0);
        $this->setSetting('Faq', 'most_read_num_items', 0);
        $this->setSetting('Faq', 'related_num_items', 0);
        $this->setSetting('Faq', 'spamfilter', false);
        $this->setSetting('Faq', 'allow_feedback', false);
        $this->setSetting('Faq', 'allow_own_question', false);
        $this->setSetting('Faq', 'allow_multiple_categories', true);
        $this->setSetting('Faq', 'send_email_on_new_feedback', false);

        foreach ($this->getLanguages() as $language) {
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId == 0) {
                $this->defaultCategoryId = $this->addCategory($language, 'Default', 'default');
            }

            // check if a page for the faq already exists in this language
            $faqPageExists = (bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($faqId, $language)
            );

            if (!$faqPageExists) {
                // insert page
                $this->insertPage(
                    array(
                        'title' => 'FAQ',
                        'language' => $language
                    ),
                    null,
                    array(
                         'extra_id' => $faqId)
                );
            }
        }

        $this->insertWidget();

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationFaqId = $this->setNavigation($navigationModulesId, 'Faq');
        $this->setNavigation(
            $navigationFaqId,
            'Questions',
            'faq/index',
            array('faq/add', 'faq/edit')
        );
        $this->setNavigation(
            $navigationFaqId,
            'Categories',
            'faq/categories',
            array('faq/add_category', 'faq/edit_category')
        );
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Faq', 'faq/settings');
    }
}
