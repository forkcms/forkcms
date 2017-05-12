<?php

namespace Backend\Modules\Pages\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the pages module
 */
class Installer extends ModuleInstaller
{
    private function importData(): void
    {
        // insert required pages
        $this->insertPages();

        // install example data if requested
        if ($this->installExample()) {
            $this->installExampleData();
        }
    }

    private function insertPages(): void
    {
        // get extra ids
        $extras = [];
        $extras['search'] = $this->insertExtra('Search', ModuleExtraType::block(), 'Search', null, null, false, 2000);
        $extras['search_form'] = $this->insertExtra(
            'Search',
            ModuleExtraType::widget(),
            'SearchForm',
            'Form',
            null,
            false,
            2001
        );
        $extras['sitemap_widget_sitemap'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'Sitemap',
            'Sitemap',
            null,
            false,
            1
        );
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Navigation', 'PreviousNextNavigation');

        $extras['subpages_widget'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'Subpages',
            'Subpages',
            ['template' => 'SubpagesDefault.html.twig'],
            false,
            2
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if (!(bool) $this->getDB()->getVar('SELECT 1 FROM pages WHERE language = ? LIMIT 1', [$language])) {
                // insert homepage
                $this->insertPage(
                    [
                        'id' => 1,
                        'parent_id' => 0,
                        'template_id' => $this->getTemplateId('home'),
                        'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                        'language' => $language,
                        'allow_move' => 'N',
                        'allow_delete' => 'N',
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // insert sitemap
                $this->insertPage(
                    [
                        'id' => 2,
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('Sitemap', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'type' => 'footer',
                        'language' => $language,
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sitemap.txt'],
                    ['extra_id' => $extras['sitemap_widget_sitemap']],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // insert disclaimer
                $this->insertPage(
                    [
                        'id' => 3,
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('Disclaimer', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'type' => 'footer',
                        'language' => $language,
                    ],
                    ['data' => ['seo_index' => 'noindex', 'seo_follow' => 'nofollow']],
                    [
                        'html' => __DIR__ . '/Data/' . $language .
                                  '/disclaimer.txt',
                    ],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // insert 404
                $this->insertPage(
                    [
                        'id' => 404,
                        'title' => '404',
                        'template_id' => $this->getTemplateId('error'),
                        'type' => 'root',
                        'language' => $language,
                        'allow_move' => 'N',
                        'allow_delete' => 'N',
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/404.txt'],
                    ['extra_id' => $extras['sitemap_widget_sitemap']],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );
            }
        }
    }

    public function install(): void
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'pages' as a module
        $this->addModule('Pages');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // import data
        $this->importData();

        // set rights
        $this->setRights();

        // set navigation
        $this->setNavigation(null, 'Pages', 'pages/index', ['pages/add', 'pages/edit'], 2);

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Pages', 'pages/settings');
    }

    private function installExampleData(): void
    {
        // insert/get extra ids
        $extras = [];
        $extras['blog_block'] = $this->insertExtra('Blog', ModuleExtraType::block(), 'Blog', null, null, false, 1000);
        $extras['blog_widget_recent_comments'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentComments',
            'RecentComments',
            null,
            false,
            1001
        );
        $extras['blog_widget_categories'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'Categories',
            'Categories',
            null,
            false,
            1002
        );
        $extras['blog_widget_archive'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'Archive',
            'Archive',
            null,
            false,
            1003
        );
        $extras['blog_widget_recent_articles_full'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentArticlesFull',
            'RecentArticlesFull',
            null,
            false,
            1004
        );
        $extras['blog_widget_recent_articles_list'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentArticlesList',
            'RecentArticlesList',
            null,
            false,
            1005
        );
        $extras['search'] = $this->insertExtra('Search', ModuleExtraType::block(), 'Search', null, null, false, 2000);
        $extras['search_form'] = $this->insertExtra(
            'Search',
            ModuleExtraType::widget(),
            'SearchForm',
            'Form',
            null,
            false,
            2001
        );
        $extras['sitemap_widget_sitemap'] = $this->insertExtra(
            'Pages',
            ModuleExtraType::widget(),
            'Sitemap',
            'Sitemap',
            null,
            false,
            1
        );
        $extras['subpages_widget'] = $this->insertExtra(
            'Pages',
            ModuleExtraType::widget(),
            'Subpages',
            'Subpages',
            ['template' => 'SubpagesDefault.html.twig'],
            false,
            2
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if (!(bool) $this->getDB()->getVar(
                'SELECT 1 FROM pages WHERE language = ? AND id > ? LIMIT 1',
                [$language, 404]
            )
            ) {
                // re-insert homepage
                $this->insertPage(
                    [
                        'id' => 1,
                        'parent_id' => 0,
                        'template_id' => $this->getTemplateId('home'),
                        'title' => \SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                        'language' => $language,
                        'allow_move' => 'N',
                        'allow_delete' => 'N',
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                    ['extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'],
                    ['extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'right'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // blog
                $this->insertPage(
                    [
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('Blog', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $extras['blog_block']],
                    ['extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'left'],
                    ['extra_id' => $extras['blog_widget_categories'], 'position' => 'left'],
                    ['extra_id' => $extras['blog_widget_archive'], 'position' => 'left'],
                    ['extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'left'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // about us parent
                $aboutUsId = $this->insertPage(
                    [
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('AboutUs', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'parent_id' => 1,
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $extras['subpages_widget']],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // location
                $this->insertPage(
                    [
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('Location', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'parent_id' => $aboutUsId,
                        'language' => $language,
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                    ['html' => __DIR__ . '/Data/' . $language . '/sample2.txt'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // about us child
                $this->insertPage(
                    [
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('AboutUs', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'parent_id' => $aboutUsId,
                        'language' => $language,
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                    ['html' => __DIR__ . '/Data/' . $language . '/sample2.txt'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // history
                $this->insertPage(
                    [
                        'title' => \SpoonFilter::ucfirst(
                            $this->getLocale('History', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'parent_id' => 1,
                        'language' => $language,
                    ],
                    null,
                    ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                    ['html' => __DIR__ . '/Data/' . $language . '/sample2.txt'],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );

                // insert lorem ipsum test page
                $this->insertPage(
                    [
                        'title' => 'Lorem ipsum',
                        'type' => 'root',
                        'language' => $language,
                        'hidden' => 'Y',
                    ],
                    ['data' => ['seo_index' => 'noindex', 'seo_follow' => 'nofollow']],
                    [
                        'html' => __DIR__ . '/Data/' . $language . '/lorem_ipsum.txt',
                    ],
                    ['extra_id' => $extras['search_form'], 'position' => 'top']
                );
            }
        }
    }

    private function setRights(): void
    {
        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'GetInfo');
        $this->setActionRights(1, $this->getModule(), 'Move');

        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'UploadFile');
        $this->setActionRights(1, $this->getModule(), 'RemoveUploadedFile');

        $this->setActionRights(1, $this->getModule(), 'Settings');
    }
}
