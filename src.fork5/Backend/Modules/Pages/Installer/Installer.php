<?php

namespace Backend\Modules\Pages\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Common\Doctrine\Entity\CreateSchema;
use SpoonFilter;

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
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureEntities();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Pages"
        $this->setNavigation(null, $this->getModule(), 'pages/page_index', ['pages/page_add', 'pages/page_edit'], 2);

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'pages/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'PageAdd');
        $this->setActionRights(1, $this->getModule(), 'PageDelete');
        $this->setActionRights(1, $this->getModule(), 'PageEdit');
        $this->setActionRights(1, $this->getModule(), 'PageIndex');
        $this->setActionRights(1, $this->getModule(), 'PageCopyToOtherLanguage');
        $this->setActionRights(1, $this->getModule(), 'Move'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    /**
     * @todo: When we have Page entities, use DataFixtures instead of this method.
     */
    private function installExampleData(): void
    {
        // insert/get extra ids
        $extras = [];
        $extras['blog_block'] = $this->insertExtra('Blog', ModuleExtraType::block(), 'Blog');
        $extras['blog_widget_recent_comments'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentComments',
            'RecentComments'
        );
        $extras['blog_widget_categories'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'Categories',
            'Categories'
        );
        $extras['blog_widget_archive'] = $this->insertExtra('Blog', ModuleExtraType::widget(), 'Archive', 'Archive');
        $extras['blog_widget_recent_articles_full'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentArticlesFull',
            'RecentArticlesFull'
        );
        $extras['blog_widget_related_articles'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RelatedArticles',
            'RelatedArticles'
        );
        $extras['blog_widget_recent_articles_list'] = $this->insertExtra(
            'Blog',
            ModuleExtraType::widget(),
            'RecentArticlesList',
            'RecentArticlesList'
        );
        if (in_array('Faq', $this->getVariable('selected_modules'))) {
            $extras['faq_block'] = $this->insertExtra('Faq', ModuleExtraType::block(), 'Faq');
        }
        if (in_array('Mailmotor', $this->getVariable('selected_modules'))) {
            $extras['mailmotor_subscribe'] = $this->insertExtra(
                'Mailmotor',
                ModuleExtraType::block(),
                'SubscribeForm',
                'Subscribe'
            );
            $extras['mailmotor_unsubscribe'] = $this->insertExtra(
                'Mailmotor',
                ModuleExtraType::block(),
                'UnsubscribeForm',
                'Unsubscribe'
            );
        }
        $extras['tags_block'] = $this->insertExtra('Tags', ModuleExtraType::block(), 'Tags');
        if (in_array('Profiles', $this->getVariable('selected_modules'))) {
            $extras['profiles_forgot_password'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'ForgotPassword',
                'ForgotPassword'
            );
            $extras['profiles_block'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'Dashboard'
            );
            $extras['profiles_login'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'Login',
                'Login'
            );
            $extras['profiles_logout'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'Logout',
                'Logout'
            );
            $extras['profiles_change_email'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'ChangeEmail',
                'ChangeEmail'
            );
            $extras['profiles_change_password'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'ChangePassword',
                'ChangePassword'
            );
            $extras['profiles_settings'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'Settings',
                'Settings'
            );
            $extras['profiles_register'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'Register',
                'Register'
            );
            $extras['profiles_resend_activation'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::block(),
                'ResendActivation',
                'ResendActivation'
            );
            $extras['profiles_login_box'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::widget(),
                'LoginBox',
                'LoginBox'
            );
            $extras['profiles_login_link'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::widget(),
                'LoginLink',
                'LoginLink'
            );
            $extras['profiles_secure_page'] = $this->insertExtra(
                'Profiles',
                ModuleExtraType::widget(),
                'SecurePage',
                'SecurePage'
            );
        }

        // loop languages
        foreach ($this->getLanguages() as $language) {
            // insert modules page
            $modulesPageId = $this->insertPage(
                [
                    'id' => 4,
                    'title' => SpoonFilter::ucfirst(
                        $this->getLocale('Modules', 'Core', $language, 'lbl', 'Frontend')
                    ),
                    'type' => 'page',
                    'language' => $language,
                    'parent_id' => Page::HOME_PAGE_ID,
                ],
                null,
                ['extra_id' => $this->getExtraId('subpages')],
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
            );

            // check if pages already exist for this language
            if (!$this->hasPage($language)) {
                // re-insert homepage
                $this->insertPage(
                    [
                        'id' => Page::HOME_PAGE_ID,
                        'parent_id' => Page::NO_PARENT_PAGE_ID,
                        'template_id' => $this->getTemplateId('home'),
                        'title' => SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language)),
                        'language' => $language,
                        'allow_move' => false,
                        'allow_delete' => false,
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
                        'title' => SpoonFilter::ucfirst(
                            $this->getLocale('Blog', 'Core', $language, 'lbl', 'Frontend')
                        ),
                        'language' => $language,
                        'parent_id' => $modulesPageId,
                    ],
                    null,
                    ['extra_id' => $extras['blog_block']],
                    ['extra_id' => $extras['blog_widget_recent_comments'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_categories'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_archive'], 'position' => 'main'],
                    ['extra_id' => $extras['blog_widget_recent_articles_list'], 'position' => 'main'],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                );

                // faq
                if (in_array('Faq', $this->getVariable('selected_modules'))) {
                    $this->insertPage(
                        [
                            'title' => 'FAQ',
                            'language' => $language,
                            'parent_id' => $modulesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['faq_block']]
                    );
                }

                // mailmotor
                if (in_array('Mailmotor', $this->getVariable('selected_modules'))) {
                    $newslettersPageId = $this->insertPage(
                        [
                            'title' => 'Newsletters',
                            'language' => $language,
                            'parent_id' => $modulesPageId,
                        ]
                    );
                    $this->insertPage(
                        ['parent_id' => $newslettersPageId, 'title' => 'Subscribe', 'language' => $language],
                        null,
                        ['extra_id' => $extras['mailmotor_subscribe'], 'position' => 'main']
                    );
                    $this->insertPage(
                        ['parent_id' => $newslettersPageId, 'title' => 'Unsubscribe', 'language' => $language],
                        null,
                        ['extra_id' => $extras['mailmotor_unsubscribe'], 'position' => 'main']
                    );
                }

                // tags
                $this->insertPage(
                    [
                        'title' => 'Tags',
                        'language' => $language,
                        'parent_id' => $modulesPageId,
                    ],
                    null,
                    ['extra_id' => $extras['tags_block'], 'position' => 'main'],
                    ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                );

                // profiles
                if (in_array('Profiles', $this->getVariable('selected_modules'))) {
                    $profilesPageId = $this->insertPage(
                        [
                            'title' => 'Profiles',
                            'language' => $language,
                            'parent_id' => $modulesPageId,
                        ],
                        null,
                        ['extra_id' => $this->getExtraId('subpages')],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('ForgotPassword', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_forgot_password'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst(
                                $this->getLocale('ResendActivation', 'Core', $language, 'lbl', 'Backend')
                            ),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_resend_activation'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('Login', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_login'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('Register', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_register'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('Logout', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_logout'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('Profile', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_block'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst(
                                $this->getLocale('ProfileSettings', 'Core', $language, 'lbl', 'Backend')
                            ),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_settings'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('ChangeEmail', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_change_email'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                    $this->insertPage(
                        [
                            'title' => SpoonFilter::ucfirst($this->getLocale('ChangePassword', 'Core', $language, 'lbl', 'Backend')),
                            'language' => $language,
                            'parent_id' => $profilesPageId,
                        ],
                        null,
                        ['extra_id' => $extras['profiles_change_password'], 'position' => 'main'],
                        ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
                    );
                }
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
                    'id' => Page::HOME_PAGE_ID,
                    'parent_id' => Page::NO_PARENT_PAGE_ID,
                    'template_id' => $this->getTemplateId('home'),
                    'title' => SpoonFilter::ucfirst($this->getLocale('Home', 'Core', $language, 'lbl', 'Backend')),
                    'language' => $language,
                    'allow_move' => false,
                    'allow_delete' => false,
                ],
                null,
                ['html' => __DIR__ . '/Data/' . $language . '/sample1.txt'],
                ['extra_id' => $this->getExtraId('search_form'), 'position' => 'top']
            );

            // insert sitemap
            $this->insertPage(
                [
                    'id' => 2,
                    'title' => SpoonFilter::ucfirst(
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
                    'title' => SpoonFilter::ucfirst(
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
                    'id' => Page::ERROR_PAGE_ID,
                    'title' => '404',
                    'template_id' => $this->getTemplateId('error'),
                    'type' => 'root',
                    'language' => $language,
                    'allow_move' => false,
                    'allow_delete' => false,
                    'allow_children' => false,
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
            'SELECT 1 FROM PagesPage WHERE locale = ? AND id > ? LIMIT 1',
            [$language, Page::ERROR_PAGE_ID]
        );
    }

    private function configureEntities(): void
    {
        Model::get(CreateSchema::class)->forEntityClasses(
            [
                PageBlock::class,
                Page::class,
            ]
        );
    }
}
