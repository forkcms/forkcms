<?php

namespace Backend\Modules\Pages\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the pages module
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Installer extends ModuleInstaller
{
    /**
     * Import the data
     */
    private function importData()
    {
        // insert required pages
        $this->insertPages();

        // install example data if requested
        if ($this->installExample()) {
            $this->installExampleData();
        }
    }

    /**
     * Insert the pages
     */
    private function insertPages()
    {
        // get extra ids
        $extras['search'] = $this->insertExtra('Search', 'block', 'Search', null, null, 'N', 2000);
        $extras['search_form'] = $this->insertExtra('Search', 'widget', 'SearchForm', 'Form', null, 'N', 2001);
        $extras['sitemap_widget_sitemap'] = $this->insertExtra('Pages', 'widget', 'Sitemap', 'Sitemap', null, 'N', 1);
        $this->insertExtra('Pages', 'widget', 'Navigation', 'PreviousNextNavigation');

        $extras['subpages_widget'] = $this->insertExtra(
            'Pages',
            'widget',
            'Subpages',
            'Subpages',
            serialize(array('template' => 'SubpagesDefault.tpl')),
            'N',
            2
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if (!(bool) $this->getDB()->getVar('SELECT 1 FROM pages WHERE language = ? LIMIT 1', array($language))) {
                // insert homepage
                $this->insertPage(
                    array(
                         'id' => 1,
                         'parent_id' => 0,
                         'template_id' => $this->getTemplateId('home'),
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                         'language' => $language,
                         'allow_move' => 'N',
                         'allow_delete' => 'N'
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample1.txt'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // insert sitemap
                $this->insertPage(
                    array(
                         'id' => 2,
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Sitemap', 'Core', $language, 'lbl', 'Frontend')),
                         'type' => 'footer',
                         'language' => $language
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sitemap.txt'),
                    array('extra_id' => $extras['sitemap_widget_sitemap']),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // insert disclaimer
                $this->insertPage(
                    array(
                         'id' => 3,
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Disclaimer', 'Core', $language, 'lbl', 'Frontend')),
                         'type' => 'footer',
                         'language' => $language
                    ),
                    array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
                    array(
                         'html' => dirname(__FILE__) . '/Data/' . $language .
                                   '/disclaimer.txt'
                    ),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // insert 404
                $this->insertPage(
                    array(
                         'id' => 404,
                         'title' => '404',
                         'type' => 'root',
                         'language' => $language,
                         'allow_move' => 'N',
                         'allow_delete' => 'N'
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/404.txt'),
                    array('extra_id' => $extras['sitemap_widget_sitemap']),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );
            }
        }
    }

    /**
     * Install this module.
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'pages' as a module
        $this->addModule('Pages');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // import data
        $this->importData();

        // set rights
        $this->setRights();

        // set navigation
        $this->setNavigation(null, 'Pages', 'pages/index', array('pages/add', 'pages/edit'), 2);

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Pages', 'pages/settings');
    }

    /**
     * Install example data
     */
    private function installExampleData()
    {
        // insert/get extra ids
        $extras['blog_block'] = $this->insertExtra('Blog', 'block', 'Blog', null, null, 'N', 1000);
        $extras['blog_widget_recent_comments'] = $this->insertExtra(
            'Blog',
            'widget',
            'RecentComments',
            'RecentComments',
            null,
            'N',
            1001
        );
        $extras['blog_widget_categories'] = $this->insertExtra(
            'Blog',
            'widget',
            'Categories',
            'Categories',
            null,
            'N',
            1002
        );
        $extras['blog_widget_archive'] = $this->insertExtra('Blog', 'widget', 'Archive', 'Archive', null, 'N', 1003);
        $extras['blog_widget_recent_articles_full'] = $this->insertExtra(
            'Blog',
            'widget',
            'RecentArticlesFull',
            'RecentArticlesFull',
            null,
            'N',
            1004
        );
        $extras['blog_widget_recent_articles_list'] = $this->insertExtra(
            'Blog',
            'widget',
            'RecentArticlesList',
            'RecentArticlesList',
            null,
            'N',
            1005
        );
        $extras['search'] = $this->insertExtra('Search', 'block', 'Search', null, null, 'N', 2000);
        $extras['search_form'] = $this->insertExtra('Search', 'widget', 'SearchForm', 'Form', null, 'N', 2001);
        $extras['sitemap_widget_sitemap'] = $this->insertExtra('Pages', 'widget', 'Sitemap', 'Sitemap', null, 'N', 1);
        $extras['subpages_widget'] = $this->insertExtra(
            'Pages',
            'widget',
            'Subpages',
            'Subpages',
            serialize(array('template' => 'SubpagesDefault.tpl')),
            'N',
            2
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1 FROM pages WHERE language = ? AND id > ? LIMIT 1',
                array($language, 404)
            )
            ) {
                // re-insert homepage
                $this->insertPage(
                    array(
                         'id' => 1,
                         'parent_id' => 0,
                         'template_id' => $this->getTemplateId('home'),
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                         'language' => $language,
                         'allow_move' => 'N',
                         'allow_delete' => 'N'
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample1.txt'),
                    array('extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'),
                    array('extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'right'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // blog
                $this->insertPage(
                    array(
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Blog', 'Core', $language, 'lbl', 'Frontend')),
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $extras['blog_block']),
                    array('extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'left'),
                    array('extra_id' => $extras['blog_widget_categories'], 'position' => 'left'),
                    array('extra_id' => $extras['blog_widget_archive'], 'position' => 'left'),
                    array('extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // about us parent
                $aboutUsId = $this->insertPage(
                    array(
                         'title' => \SpoonFilter::ucfirst($this->getLocale('AboutUs', 'Core', $language, 'lbl', 'Frontend')),
                         'parent_id' => 1,
                         'language' => $language
                    ),
                    null,
                    array('extra_id' => $extras['subpages_widget']),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // location
                $this->insertPage(
                    array(
                         'title' => \SpoonFilter::ucfirst($this->getLocale('Location', 'Core', $language, 'lbl', 'Frontend')),
                         'parent_id' => $aboutUsId,
                         'language' => $language
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample1.txt'),
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample2.txt'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // about us child
                $this->insertPage(
                    array(
                         'title' => \SpoonFilter::ucfirst($this->getLocale('AboutUs', 'Core', $language, 'lbl', 'Frontend')),
                         'parent_id' => $aboutUsId,
                         'language' => $language
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample1.txt'),
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample2.txt'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // history
                $this->insertPage(
                    array(
                         'title' => \SpoonFilter::ucfirst($this->getLocale('History', 'Core', $language, 'lbl', 'Frontend')),
                         'parent_id' => 1,
                         'language' => $language
                    ),
                    null,
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample1.txt'),
                    array('html' => dirname(__FILE__) . '/Data/' . $language . '/sample2.txt'),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );

                // insert lorem ipsum test page
                $this->insertPage(
                    array(
                         'title' => 'Lorem ipsum',
                         'type' => 'root',
                         'language' => $language,
                         'hidden' => 'Y'
                    ),
                    array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
                    array(
                         'html' => dirname(__FILE__) . '/Data/' . $language . '/lorem_ipsum.txt'
                    ),
                    array('extra_id' => $extras['search_form'], 'position' => 'top')
                );
            }
        }
    }

    /**
     * Set the rights
     */
    private function setRights()
    {
        // module rights
        $this->setModuleRights(1, 'Pages');

        // action rights
        $this->setActionRights(1, 'Pages', 'GetInfo');
        $this->setActionRights(1, 'Pages', 'Move');

        $this->setActionRights(1, 'Pages', 'Index');
        $this->setActionRights(1, 'Pages', 'Add');
        $this->setActionRights(1, 'Pages', 'Delete');
        $this->setActionRights(1, 'Pages', 'Edit');

        $this->setActionRights(1, 'Pages', 'Settings');
    }
}
