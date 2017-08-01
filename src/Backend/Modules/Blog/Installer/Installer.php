<?php

namespace Backend\Modules\Blog\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the blog module
 */
class Installer extends ModuleInstaller
{
    /** @var int - Default category id */
    private $defaultCategoryId;

    /** @var int */
    private $blogBlockId;

    public function install(): void
    {
        $this->addModule('Blog');
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

    private function configureBackendActionRightsForBlogArticle(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ImportWordpress');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureBackendActionRightsForBlogArticleComment(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Comments');
        $this->setActionRights(1, $this->getModule(), 'DeleteSpam');
        $this->setActionRights(1, $this->getModule(), 'EditComment');
        $this->setActionRights(1, $this->getModule(), 'MassCommentAction');
    }

    private function configureBackendActionRightsForBlogCategory(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationBlogId = $this->setNavigation($navigationModulesId, 'Blog');
        $this->setNavigation(
            $navigationBlogId,
            'Articles',
            'blog/index',
            ['blog/add', 'blog/edit', 'blog/import_wordpress']
        );
        $this->setNavigation($navigationBlogId, 'Comments', 'blog/comments', ['blog/edit_comment']);
        $this->setNavigation(
            $navigationBlogId,
            'Categories',
            'blog/categories',
            ['blog/add_category', 'blog/edit_category']
        );

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Blog', 'blog/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        // Action rights for entities
        $this->configureBackendActionRightsForBlogArticle();
        $this->configureBackendActionRightsForBlogCategory();
        $this->configureBackendActionRightsForBlogArticleComment();

        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureBackendWidgets(): void
    {
        $this->insertDashboardWidget('Blog', 'Comments');
    }

    private function configureFrontendExtras(): void
    {
        $this->blogBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'Blog');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Archive', 'Archive');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Categories', 'Categories');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentArticlesFull', 'RecentArticlesFull');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentArticlesList', 'RecentArticlesList');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'RecentComments', 'RecentComments');
    }

    private function configureFrontendPages(): void
    {
        // get search extra id
        $searchId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // fetch current categoryId
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId === 0) {
                // add category
                $this->defaultCategoryId = $this->insertCategory($language, 'Default', 'default');
            }

            // RSS settings
            $this->setSetting($this->getModule(), 'rss_meta_' . $language, true);
            $this->setSetting($this->getModule(), 'rss_title_' . $language, 'RSS');
            $this->setSetting($this->getModule(), 'rss_description_' . $language, '');

            // check if a page for blog already exists in this language
            if (!$this->hasPageWithBlogBlock($language)) {
                $this->insertPage(
                    ['title' => 'Blog', 'language' => $language],
                    null,
                    ['extra_id' => $this->blogBlockId, 'position' => 'main'],
                    ['extra_id' => $searchId, 'position' => 'top']
                );
            }

            if ($this->installExample()) {
                $this->installExampleData($language);
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'allow_comments', true);
        $this->setSetting($this->getModule(), 'max_num_revisions', 20);
        $this->setSetting($this->getModule(), 'moderation', true);
        $this->setSetting($this->getModule(), 'overview_num_items', 10);
        $this->setSetting($this->getModule(), 'recent_articles_full_num_items', 3);
        $this->setSetting($this->getModule(), 'recent_articles_list_num_items', 5);
        $this->setSetting($this->getModule(), 'requires_akismet', true);
        $this->setSetting($this->getModule(), 'spamfilter', false);
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language The language to use.
     *
     * @return int
     */
    private function getCategory(string $language): int
    {
        // @todo: Replace this with a BlogCategoryRepository method when it exists.
        return (int) $this->getDatabase()->getVar(
            'SELECT id FROM blog_categories WHERE language = ?',
            [$language]
        );
    }

    private function getSearchWidgetId(): int
    {
        // @todo: Replace this with a ModuleExtraRepository method when it exists.
        return (int) $this->getDatabase()->getVar(
            'SELECT id FROM modules_extras
             WHERE module = ? AND type = ? AND action = ?',
            ['Search', ModuleExtraType::widget(), 'Form']
        );
    }

    private function hasPageWithBlogBlock(string $language): bool
    {
        // @todo: Replace with a PageRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM pages AS p
             INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
             WHERE b.extra_id = ? AND p.language = ?
             LIMIT 1',
            [$this->blogBlockId, $language]
        );
    }

    /**
     * Insert a category for a language
     *
     * @todo: Replace this with a BlogCategoryRepository method when it exists.
     *
     * @param string $language The language to use.
     * @param string $title The title of the category.
     * @param string $url The URL for the category.
     *
     * @return int
     */
    private function insertCategory(string $language, string $title, string $url): int
    {
        $item = [];
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = $language;
        $item['title'] = $title;

        return (int) $this->getDatabase()->insert('blog_categories', $item);
    }

    /**
     * @todo: this method should be rewritten to use DataFixtures.
     */
    private function installExampleData(string $language): void
    {
        // get database instance
        $database = $this->getDatabase();

        // check if blogposts already exist in this language
        if (!(bool) $database->getVar(
            'SELECT 1
             FROM blog_posts
             WHERE language = ?
             LIMIT 1',
            [$language]
        )
        ) {
            // insert sample blogpost 1
            $database->insert(
                'blog_posts',
                [
                    'id' => 1,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta(
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'nunc-sediam-est'
                    ),
                    'language' => $language,
                    'title' => 'Nunc sediam est',
                    'introduction' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00'),
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'edited_on' => gmdate('Y-m-d H:i:00'),
                    'hidden' => false,
                    'allow_comments' => true,
                    'num_comments' => '2',
                ]
            );

            // add Search index blogpost 1
            $this->addSearchIndex(
                $this->getModule(),
                1,
                [
                    'title' => 'Nunc sediam est',
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                ],
                $language
            );

            // insert sample blogpost 2
            $database->insert(
                'blog_posts',
                [
                    'id' => 2,
                    'category_id' => $this->defaultCategoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
                    'language' => $language,
                    'title' => 'Lorem ipsum',
                    'introduction' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'hidden' => false,
                    'allow_comments' => true,
                    'num_comments' => '0',
                ]
            );

            // add Search index blogpost 2
            $this->addSearchIndex(
                $this->getModule(),
                2,
                [
                    'title' => 'Lorem ipsum',
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                ],
                $language
            );

            // insert example comment 1
            $database->insert(
                'blog_comments',
                [
                    'post_id' => 1,
                    'language' => $language,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Davy Hellemans',
                    'email' => 'forkcms-sample@spoon-library.com',
                    'website' => 'http://www.spoon-library.com',
                    'text' => 'awesome!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null,
                ]
            );

            // insert example comment 2
            $database->insert(
                'blog_comments',
                [
                    'post_id' => 1,
                    'language' => $language,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Tijs Verkoyen',
                    'email' => 'forkcms-sample@sumocoders.be',
                    'website' => 'https://www.sumocoders.be',
                    'text' => 'wicked!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null,
                ]
            );
        }
    }
}
