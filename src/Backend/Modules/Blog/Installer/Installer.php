<?php

namespace Backend\Modules\Blog\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the blog module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Installer extends ModuleInstaller
{
    /**
     * Add a category for a language
     *
     * @param string $language The language to use.
     * @param string $siteId   The siteId to use.
     * @param string $title    The title of the category.
     * @param string $url      The URL for the category.
     * @return int
     */
    private function addCategory($language, $siteId, $title, $url)
    {
        $item = array();
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = (string) $language;
        $item['site_id'] = (int) $siteId;
        $item['title'] = (string) $title;

        return (int) $this->getDB()->insert('blog_categories', $item);
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language The language to use.
     * @return int
     */
    private function getCategory($language, $siteId)
    {
        return (int) $this->getDB()->getVar(
            'SELECT id
             FROM blog_categories
             WHERE language = ? AND site_id = ?',
            array((string) $language, (int) $siteId)
        );
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $comments = array(
            'column' => 'right',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        $this->insertDashboardWidget('Blog', 'Comments', $comments);
    }

    /**
     * Install the module
     */
    public function install()
    {
        // add 'blog' as a module
        $this->addModule('Blog');

        // load database scheme and locale
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // general settings
        $this->setSetting('Blog', 'allow_comments', true);
        $this->setSetting('Blog', 'requires_akismet', true);
        $this->setSetting('Blog', 'spamfilter', false);
        $this->setSetting('Blog', 'moderation', true);
        $this->setSetting('Blog', 'ping_services', true);
        $this->setSetting('Blog', 'overview_num_items', 10);
        $this->setSetting('Blog', 'recent_articles_full_num_items', 3);
        $this->setSetting('Blog', 'recent_articles_list_num_items', 5);
        $this->setSetting('Blog', 'max_num_revisions', 20);

        $this->makeSearchable('Blog');

        // rights
        $this->setModuleRights(1, 'Blog');
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

        $this->insertBackendNavigation();
        $extras = $this->insertExtras();
        $this->insertPages($extras);
    }

    protected function insertBackendNavigation()
    {
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
    }

    protected function insertExtras()
    {
        $extras = array();

        // add extra's
        $extras['blog'] = $this->insertExtra('Blog', 'block', 'Blog', null, null, 'N', 1000);
        $this->insertExtra('Blog', 'widget', 'RecentComments', 'RecentComments', null, 'N', 1001);
        $this->insertExtra('Blog', 'widget', 'Categories', 'Categories', null, 'N', 1002);
        $this->insertExtra('Blog', 'widget', 'Archive', 'Archive', null, 'N', 1003);
        $this->insertExtra('Blog', 'widget', 'RecentArticlesFull', 'RecentArticlesFull', null, 'N', 1004);
        $this->insertExtra('Blog', 'widget', 'RecentArticlesList', 'RecentArticlesList', null, 'N', 1005);

        // get search extra id
        $extras['search'] = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras
             WHERE module = ? AND type = ? AND action = ?',
            array('Search', 'widget', 'Form')
        );

        return $extras;
    }

    protected function insertPages($extras)
    {
        // loop languages
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages($site['id']) as $language) {
                // fetch current categoryId
                $categoryId = $this->getCategory($language, $site['id']);

                // add a new category if there is none yet exists
                if ($categoryId == 0) {
                    $categoryId = $this->addCategory($language, $site['id'], 'Default', 'default');
                }

                // feedburner URL
                $this->setSetting('Blog', 'feedburner_url', '', $language, $site['id']);
                $this->setSetting('Blog', 'rss_meta', true, $language, $site['id']);
                $this->setSetting('Blog', 'rss_title', 'RSS', $language, $site['id']);
                $this->setSetting('Blog', 'rss_description', '', $language, $site['id']);

                // check if a page for blog already exists in this language
                if (!(bool) $this->getDB()->getVar(
                    'SELECT 1
                     FROM pages AS p
                     INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                     WHERE b.extra_id = ? AND p.language = ? AND p.site_id = ?
                     LIMIT 1',
                    array($extras['blog'], $language, $site['id'])
                )
                ) {
                    $this->insertPage(
                        array(
                            'title' => 'Blog',
                            'language' => $language,
                            'site_id' => $site['id'],
                        ),
                        null,
                        array('extra_id' => $extras['blog'], 'position' => 'main'),
                        array('extra_id' => $extras['search'], 'position' => 'top')
                    );
                }

                if ($this->installExample()) {
                    $this->installExampleData($language, $site['id'], $categoryId);
                }
            }
        }
    }

    /**
     * Install example data
     *
     * @param string $language   The language to use.
     * @param int    $siteId     The siteid to insert items for.
     * @param int    $categoryId The categoryId to insert blogposts in
     */
    private function installExampleData($language, $siteId, $categoryId)
    {
        // get db instance
        $db = $this->getDB();

        // check if blogposts already exist in this language
        if (!(bool) $db->getVar(
            'SELECT 1
             FROM blog_posts
             WHERE language = ? AND site_id = ?
             LIMIT 1',
            array($language, $siteId)
        )
        ) {
            // insert sample blogpost 1
            $db->insert(
                'blog_posts',
                array(
                    'id' => 1,
                    'category_id' => $categoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta(
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'Nunc sediam est',
                        'nunc-sediam-est'
                    ),
                    'language' => $language,
                    'site_id' => $siteId,
                    'title' => 'Nunc sediam est',
                    'introduction' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Blog/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Blog/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00'),
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'edited_on' => gmdate('Y-m-d H:i:00'),
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '2'
                )
            );

            // insert sample blogpost 2
            $db->insert(
                'blog_posts',
                array(
                    'id' => 2,
                    'category_id' => $categoryId,
                    'user_id' => $this->getDefaultUserID(),
                    'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
                    'language' => $language,
                    'site_id' => $siteId,
                    'title' => 'Lorem ipsum',
                    'introduction' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Blog/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'text' => file_get_contents(
                        PATH_WWW . '/src/Backend/Modules/Blog/Installer/Data/' . $language . '/sample1.txt'
                    ),
                    'status' => 'active',
                    'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
                    'hidden' => 'N',
                    'allow_comments' => 'Y',
                    'num_comments' => '0'
                )
            );

            // insert example comment 1
            $db->insert(
                'blog_comments',
                array(
                    'post_id' => 1,
                    'language' => $language,
                    'site_id' => $siteId,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Davy Hellemans',
                    'email' => 'forkcms-sample@spoon-library.com',
                    'website' => 'http://www.spoon-library.com',
                    'text' => 'awesome!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null
                )
            );

            // insert example comment 2
            $db->insert(
                'blog_comments',
                array(
                    'post_id' => 1,
                    'language' => $language,
                    'site_id' => $siteId,
                    'created_on' => gmdate('Y-m-d H:i:00'),
                    'author' => 'Tijs Verkoyen',
                    'email' => 'forkcms-sample@sumocoders.be',
                    'website' => 'http://www.sumocoders.be',
                    'text' => 'wicked!',
                    'type' => 'comment',
                    'status' => 'published',
                    'data' => null
                )
            );
        }
    }
}
