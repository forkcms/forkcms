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
    /**
     * Default category id
     *
     * @var int
     */
    private $defaultCategoryId;

    /**
     * Add a category for a language
     *
     * @param string $language The language to use.
     * @param string $title    The title of the category.
     * @param string $url      The URL for the category.
     *
     * @return int
     */
    private function addCategory($language, $title, $url)
    {
        $item = array();
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = (string) $language;
        $item['title'] = (string) $title;

        return (int) $this->getDB()->insert('blog_categories', $item);
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language The language to use.
     *
     * @return int
     */
    private function getCategory($language)
    {
        return (int) $this->getDB()->getVar(
            'SELECT id FROM blog_categories WHERE language = ?',
            array((string) $language)
        );
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $this->insertDashboardWidget('Blog', 'Comments');
    }

    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'blog' as a module
        $this->addModule('Blog');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // general settings
        $this->setSetting('Blog', 'allow_comments', true);
        $this->setSetting('Blog', 'requires_akismet', true);
        $this->setSetting('Blog', 'spamfilter', false);
        $this->setSetting('Blog', 'moderation', true);
        $this->setSetting('Blog', 'overview_num_items', 10);
        $this->setSetting('Blog', 'recent_articles_full_num_items', 3);
        $this->setSetting('Blog', 'recent_articles_list_num_items', 5);
        $this->setSetting('Blog', 'max_num_revisions', 20);

        $this->makeSearchable('Blog');

        // module rights
        $this->setModuleRights(1, 'Blog');

        // action rights
        $this->setActionRights(1, 'Blog', 'AddCategory');
        $this->setActionRights(1, 'Blog', 'Add');
        $this->setActionRights(1, 'Blog', 'Categories');
        $this->setActionRights(1, 'Blog', 'Comments');
        $this->setActionRights(1, 'Blog', 'DeleteCategory');
        $this->setActionRights(1, 'Blog', 'DeleteSpam');
        $this->setActionRights(1, 'Blog', 'Delete');
        $this->setActionRights(1, 'Blog', 'EditCategory');
        $this->setActionRights(1, 'Blog', 'EditComment');
        $this->setActionRights(1, 'Blog', 'Edit');
        $this->setActionRights(1, 'Blog', 'ImportWordpress');
        $this->setActionRights(1, 'Blog', 'Index');
        $this->setActionRights(1, 'Blog', 'MassCommentAction');
        $this->setActionRights(1, 'Blog', 'Settings');

        // insert dashboard widget
        $this->insertWidget();

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationBlogId = $this->setNavigation($navigationModulesId, 'Blog');
        $this->setNavigation(
            $navigationBlogId,
            'Articles',
            'blog/index',
            array('blog/add', 'blog/edit', 'blog/import_wordpress')
        );
        $this->setNavigation($navigationBlogId, 'Comments', 'blog/comments', array('blog/edit_comment'));
        $this->setNavigation(
            $navigationBlogId,
            'Categories',
            'blog/categories',
            array('blog/add_category', 'blog/edit_category')
        );

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Blog', 'blog/settings');

        // add extra's
        $blogId = $this->insertExtra('Blog', ModuleExtraType::block(), 'Blog', null, null, 'N', 1000);
        $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentComments', 'RecentComments', null, 'N', 1001);
        $this->insertExtra('Blog', ModuleExtraType::widget(), 'Categories', 'Categories', null, 'N', 1002);
        $this->insertExtra('Blog', ModuleExtraType::widget(), 'Archive', 'Archive', null, 'N', 1003);
        $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentArticlesFull', 'RecentArticlesFull', null, 'N', 1004);
        $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentArticlesList', 'RecentArticlesList', null, 'N', 1005);

        // get search extra id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras
             WHERE module = ? AND type = ? AND action = ?',
            array('Search', ModuleExtraType::WIDGET, 'Form')
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // fetch current categoryId
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId == 0) {
                // add category
                $this->defaultCategoryId = $this->addCategory($language, 'Default', 'default');
            }

            // RSS settings
            $this->setSetting('Blog', 'rss_meta_' . $language, true);
            $this->setSetting('Blog', 'rss_title_' . $language, 'RSS');
            $this->setSetting('Blog', 'rss_description_' . $language, '');

            // check if a page for blog already exists in this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($blogId, $language)
            )
            ) {
                $this->insertPage(
                    array('title' => 'Blog', 'language' => $language),
                    null,
                    array('extra_id' => $blogId, 'position' => 'main'),
                    array('extra_id' => $searchId, 'position' => 'top')
                );
            }

            if ($this->installExample()) {
                $this->installExampleData($language);
            }
        }
    }

    /**
     * Install example data
     *
     * @param string $language The language to use.
     */
    private function installExampleData($language)
    {
        // get db instance
        $db = $this->getDB();

        // check if blogposts already exist in this language
        if (!(bool) $db->getVar(
            'SELECT 1
             FROM blog_posts
             WHERE language = ?
             LIMIT 1',
            array($language)
        )
        ) {
            // insert sample blogpost 1
            $db->insert(
                'blog_posts',
                array(
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
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '2',
                )
            );

            // add Search index blogpost 1
            $this->addSearchIndex(
                'Blog',
                1,
                array(
                    'title' => 'Nunc sediam est',
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                ),
                $language
            );

            // insert sample blogpost 2
            $db->insert(
                'blog_posts',
                array(
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
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '0',
                )
            );

            // add Search index blogpost 2
            $this->addSearchIndex(
                'Blog',
                2,
                array(
                    'title' => 'Lorem ipsum',
                    'text' => file_get_contents(
                        __DIR__ . '/Data/' . $language . '/sample1.txt'
                    ),
                ),
                $language
            );

            // insert example comment 1
            $db->insert(
                'blog_comments',
                array(
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
                )
            );

            // insert example comment 2
            $db->insert(
                'blog_comments',
                array(
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
                )
            );
        }
    }
}
