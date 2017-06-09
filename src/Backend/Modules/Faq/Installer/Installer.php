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
    /** @var int */
    private $defaultCategoryId;

    /** @var int */
    private $faqBlockId;

    public function install(): void
    {
        $this->addModule('Faq');
        $this->makeSearchable($this->getModule());
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendActionRightsForFaqCategory(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'Sequence'); // AJAX
    }

    private function configureBackendActionRightsForFaqQuestion(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'SequenceQuestions'); // AJAX
    }

    private function configureBackendActionRightsForFaqQuestionFeedback(): void
    {
        $this->setActionRights(1, $this->getModule(), 'DeleteFeedback');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationFaqId = $this->setNavigation($navigationModulesId, 'Faq');
        $this->setNavigation(
            $navigationFaqId,
            'Questions',
            'faq/index',
            ['faq/add', 'faq/edit']
        );
        $this->setNavigation(
            $navigationFaqId,
            'Categories',
            'faq/categories',
            ['faq/add_category', 'faq/edit_category']
        );

        // Set navigation for "settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Faq', 'faq/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->configureBackendActionRightsForFaqCategory();
        $this->configureBackendActionRightsForFaqQuestion();
        $this->configureBackendActionRightsForFaqQuestionFeedback();
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureBackendWidgets(): void
    {
        $this->insertDashboardWidget($this->getModule(), 'Feedback');
    }

    /**
     * Configure frontend extras
     * Note: Category faq widgets will be added on the fly
     */
    private function configureFrontendExtras(): void
    {
        $this->faqBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Faq');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'MostReadQuestions', 'MostReadQuestions');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'AskOwnQuestion', 'AskOwnQuestion');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Categories', 'Categories');
    }

    private function configureFrontendPages(): void
    {
        foreach ($this->getLanguages() as $language) {
            $this->defaultCategoryId = $this->getDefaultCategoryIdForLanguage($language);

            // no category exists
            if ($this->defaultCategoryId === 0) {
                $this->defaultCategoryId = $this->insertCategory($language, 'Default', 'default');
            }

            // check if a page for the faq already exists in this language
            $faqPageExists = (bool) $this->getDB()->getVar(
                'SELECT 1
                     FROM pages AS p
                     INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                     WHERE b.extra_id = ? AND p.language = ?
                     LIMIT 1',
                [$this->faqBlockId, $language]
            );

            if (!$faqPageExists) {
                // insert page
                $this->insertPage(
                    [
                        'title' => 'FAQ',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->faqBlockId]
                );
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'allow_feedback', false);
        $this->setSetting($this->getModule(), 'allow_multiple_categories', true);
        $this->setSetting($this->getModule(), 'allow_own_question', false);
        $this->setSetting($this->getModule(), 'most_read_num_items', 5);
        $this->setSetting($this->getModule(), 'overview_num_items_per_category', 10);
        $this->setSetting($this->getModule(), 'related_num_items', 5);
        $this->setSetting($this->getModule(), 'send_email_on_new_feedback', false);
        $this->setSetting($this->getModule(), 'spamfilter', false);
    }

    private function getDefaultCategoryIdForLanguage(string $language): int
    {
        return (int) $this->getDB()->getVar(
            'SELECT id
             FROM faq_categories
             WHERE language = ?',
            [$language]
        );
    }

    /**
     * @todo: When FAQ entities are available, use DataFixtures instead of this method.
     *
     * @param string $language
     * @param string $title
     * @param string $url
     * @return int
     */
    private function insertCategory(string $language, string $title, string $url): int
    {
        $db = $this->getDB();

        // get sequence for widget
        $sequenceExtra = $db->getVar(
            'SELECT MAX(i.sequence) + 1
             FROM modules_extras AS i
             WHERE i.module = ?',
            ['faq']
        );

        // build array
        $item = [];
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
        $extra = [
            'data' => serialize(
                [
                    'id' => $item['id'],
                    'extra_label' => 'Category: ' . $item['title'],
                    'language' => $item['language'],
                    'edit_url' => '/private/' . $language . '/faq/edit_category?id=' . $item['id'],
                ]
            ),
        ];

        // update widget
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            [$item['extra_id'], $this->getModule(), ModuleExtraType::widget(), 'category_list']
        );

        return $item['id'];
    }
}
