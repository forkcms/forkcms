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
    /** @var array */
    private $extraIds;

    public function install(): void
    {
        $this->addModule('Pages');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Pages"
        $this->setNavigation(null, $this->getModule(), 'pages/index', ['pages/add', 'pages/edit'], 2);

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'pages/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'GetInfo'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Move'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'RemoveUploadedFile'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Settings');
        $this->setActionRights(1, $this->getModule(), 'UploadFile'); // AJAX
    }

    /**
     * @todo: When we have Page entities, use DataFixtures instead of this method.
     */
    private function installExampleData(): void
    {
        // insert/get extra ids
        $extras = [];
        $extras['blog_block'] = $this->insertExtra('Blog', ModuleExtraType::block(), 'Blog');
        $extras['blog_widget_recent_comments'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentComments', 'RecentComments');
        $extras['blog_widget_categories'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'Categories', 'Categories');
        $extras['blog_widget_archive'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'Archive', 'Archive');
        $extras['blog_widget_recent_articles_full'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentArticlesFull', 'RecentArticlesFull');
        $extras['blog_widget_recent_articles_list'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'RecentArticlesList', 'RecentArticlesList');

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if (!(bool) $this->hasPage($language)) {
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
                    ['extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'main'],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                    ['extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_categories'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_archive'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'main'],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                    ['extra_id' => $this->getExtraId('subpages')],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                );

                // insert lorem ipsum test page
                $this->insertPage(
                    [
                        'title' => 'Lorem ipsum',
                        'type' => 'root',
                        'language' => $language,
                        'hidden' => 'Y',
                    ],
                    ['seo_index' => 'noindex', 'seo_follow' => 'nofollow'],
                    [
                        'html' => __DIR__ . '/Data/' . $language . '/lorem_ipsum.txt',
                    ],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                );
            }
        }
    }

    private function configureFrontendExtras(): void
    {
        $this->extraIds['search'] = $this->insertExtra('Search', ModuleExtraType::block(), 'Search');
        $this->extraIds['search_form'] = $this->insertExtra('Search', ModuleExtraType::widget(), 'SearchForm', 'Form');
        $this->extraIds['sitemap'] = $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Sitemap', 'Sitemap');
        $this->extraIds['subpages'] = $this->insertExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            'Subpages',
            'Subpages',
            ['template' => 'SubpagesDefault.html.twig']
        );
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Navigation', 'PreviousNextNavigation');
    }

    private function configureFrontendPages(): void
    {
        // loop languages
        foreach ($this->getLanguages() as $language) {
            // check if pages already exist for this language
            if ($this->hasPage($language)) {
                continue;
            }

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
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                ['extra_id' => $this->getExtraId('sitemap')],
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                ['seo_index' => 'noindex', 'seo_follow' => 'nofollow'],
                [
                    'html' => __DIR__ . '/Data/' . $language .
                        '/disclaimer.txt',
                ],
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
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
                ['extra_id' => $this->getExtraId('sitemap')],
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
            );
        }

        // install example data if requested
        if ($this->installExample()) {
            $this->installExampleData();
        }
    }

    private function getExtraId(string $key): int
    {
        if (!array_key_exists($key, $this->extraIds)) {
            throw new \Exception('Key not set yet, please check your installer.');
        }

        return $this->extraIds[$key];
    }

    private function hasPage(string $language): bool
    {
        // @todo: Replace with PageRepository method when it exists.
        return (bool) $this->getDatabase()->getVar(
            'SELECT 1 FROM pages WHERE language = ? AND id > ? LIMIT 1',
            [$language, 404]
        );
    }
}
