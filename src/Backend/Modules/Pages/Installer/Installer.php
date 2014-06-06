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
        $extras = $this->insertExtras();

        // insert required pages
        $this->insertPages($extras);

        // install example data if requested
        if ($this->installExample()) {
            $this->installExampleData($extras);
        }
    }

    /**
     * Inserts extras
     *
     * @return array Key value pairs presenting widget => extras_id
     */
    private function insertExtras()
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

        return $extras;
    }

    /**
     * Insert the pages
     *
     * @param array $extras
     */
    private function insertPages($extras)
    {
        // loop languages
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages() as $language) {
                // check if pages already exist for this language
                if (!(bool) $this->getDB()->getVar(
                    'SELECT 1
                     FROM pages
                     WHERE language = ? AND site_id = ? AND id > ?
                     LIMIT 1',
                    array($language, $site['id'], 404)
                )
                ) {
                    $this->insertRequiredPages($language, $site['id'], $extras);
                }
            }
        }
    }

    /**
     * Inserts required pages for a language and a site
     * Homepage, disclaimer, sitemap and 404
     *
     * @param string $language
     * @param int $siteId
     * @param array $extras
     */
    protected function insertRequiredPages($language, $siteId, $extras)
    {
        // insert homepage
        $this->insertPage(
            array(
                 'id' => 1,
                 'parent_id' => 0,
                 'template_id' => $this->getTemplateId('home'),
                 'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                 'language' => $language,
                 'site_id' => $siteId,
                 'allow_move' => 'N',
                 'allow_delete' => 'N',
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'site_id' => $siteId,
                 'allow_move' => 'N',
                 'allow_delete' => 'N',
            ),
            null,
            array('html' => dirname(__FILE__) . '/Data/' . $language . '/404.txt'),
            array('extra_id' => $extras['sitemap_widget_sitemap']),
            array('extra_id' => $extras['search_form'], 'position' => 'top')
        );
    }

    /**
     * Install this module.
     */
    public function install()
    {
        // add 'pages' as a module
        $this->addModule('Pages');

        // load database scheme and locale
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->importData();
        $this->setRights();

        // add Backend navigation
        $this->setNavigation(null, 'Pages', 'pages/index', array('pages/add', 'pages/edit'), 2);
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Pages', 'pages/settings');
    }

    /**
     * Install example data
     *
     * @param array $extras
     */
    private function installExampleData($extras)
    {
        foreach ($this->getSites() as $site) {
            foreach ($this->getLanguages() as $language) {
                // check if pages already exist for this language
                if (!(bool) $this->getDB()->getVar(
                    'SELECT 1
                     FROM pages
                     WHERE language = ? AND site_id = ? AND id > ?
                     LIMIT 1',
                    array($language, $site['id'], 404)
                )
                ) {
                    $this->installExamplePages($language, $site['id'], $extras);
                }
            }
        }
    }

    /**
     * Inserts some pages for a language and a site
     *
     * @param string $language
     * @param int $siteId
     * @param array $extras
     */
    protected function installExamplePages($language, $siteId, $extras)
    {
        // re-insert homepage
        $this->insertPage(
            array(
                 'id' => 1,
                 'parent_id' => 0,
                 'template_id' => $this->getTemplateId('home'),
                 'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                 'language' => $language,
                 'site_id' => $siteId,
                 'allow_move' => 'N',
                 'allow_delete' => 'N',
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'language' => $language,
                 'site_id' => $siteId,
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
                 'site_id' => $siteId,
                 'hidden' => 'Y',
            ),
            array('data' => array('seo_index' => 'noindex', 'seo_follow' => 'nofollow')),
            array(
                 'html' => dirname(__FILE__) . '/Data/' . $language . '/lorem_ipsum.txt'
            ),
            array('extra_id' => $extras['search_form'], 'position' => 'top')
        );
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
