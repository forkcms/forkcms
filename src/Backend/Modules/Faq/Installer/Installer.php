<?php

namespace Backend\Modules\Faq\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the faq module
 */
class Installer extends ModuleInstaller
{
    /**
     * @var int
     */
    private $defaultCategoryId;

    /**
     * Add a category for a language
     *
     * @param string $language
     * @param string $title
     * @param string $url
     *
     * @return int
     */
    private function addCategory(string $language, string $title, string $url): int
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
        $item['extra_id'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'Faq',
            'CategoryList',
            null,
            false,
            $sequenceExtra
        );
        $item['language'] = $language;
        $item['title'] = $title;
        $item['sequence'] = 1;

        // insert category
        $item['id'] = (int) $db->insert('faq_categories', $item);

        // build data for widget
        $extra['data'] = serialize(
            array(
                'id' => $item['id'],
                'extra_label' => 'Category: ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => '/private/' . $language . '/faq/edit_category?id=' . $item['id'],
            )
        );

        // update widget
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($item['extra_id'], $this->getModule(), ModuleExtraType::WIDGET, 'category_list')
        );

        return $item['id'];
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language
     *
     * @return int
     */
    private function getCategory(string $language): int
    {
        return (int) $this->getDB()->getVar(
            'SELECT id
             FROM faq_categories
             WHERE language = ?',
            array($language)
        );
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $this->insertDashboardWidget($this->getModule(), 'Feedback');
    }

    /**
     * Install the module
     */
    public function install()
    {
        $this->importSQL(__DIR__ . '/Data/install.sql');

        $this->addModule('Faq');

        $this->importLocale(__DIR__ . '/Data/locale.xml');

        $this->makeSearchable($this->getModule());
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Sequence');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'SequenceQuestions');
        $this->setActionRights(1, $this->getModule(), 'DeleteFeedback');
        $this->setActionRights(1, $this->getModule(), 'Settings');

        $faqId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Faq');

        // Register widgets
        // Category faq widgets will be added on the fly
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'MostReadQuestions', 'MostReadQuestions');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'AskOwnQuestion', 'AskOwnQuestion');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Categories', 'Categories');

        $this->setSetting($this->getModule(), 'overview_num_items_per_category', 0);
        $this->setSetting($this->getModule(), 'most_read_num_items', 0);
        $this->setSetting($this->getModule(), 'related_num_items', 0);
        $this->setSetting($this->getModule(), 'spamfilter', false);
        $this->setSetting($this->getModule(), 'allow_feedback', false);
        $this->setSetting($this->getModule(), 'allow_own_question', false);
        $this->setSetting($this->getModule(), 'allow_multiple_categories', true);
        $this->setSetting($this->getModule(), 'send_email_on_new_feedback', false);

        foreach ($this->getLanguages() as $language) {
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId === 0) {
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
                        'language' => $language,
                    ),
                    null,
                    array('extra_id' => $faqId)
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
