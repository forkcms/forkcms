<?php

namespace Backend\Modules\Extensions\Installer;

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the extensions module.
 */
class Installer extends ModuleInstaller
{
    public function install(): void
    {
        $this->addModule('Extensions');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendForkTheme();
    }

    private function configureBackendActionRightsForModules(): void
    {
        $this->setActionRights(1, $this->getModule(), 'DetailModule');
        $this->setActionRights(1, $this->getModule(), 'InstallModule');
        $this->setActionRights(1, $this->getModule(), 'Modules');
        $this->setActionRights(1, $this->getModule(), 'UploadModule');
    }

    private function configureBackendActionRightsForTemplates(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddThemeTemplate');
        $this->setActionRights(1, $this->getModule(), 'DeleteThemeTemplate');
        $this->setActionRights(1, $this->getModule(), 'EditThemeTemplate');
        $this->setActionRights(1, $this->getModule(), 'ThemeTemplates');
    }

    private function configureBackendActionRightsForThemes(): void
    {
        $this->setActionRights(1, $this->getModule(), 'DetailTheme');
        $this->setActionRights(1, $this->getModule(), 'InstallTheme');
        $this->setActionRights(1, $this->getModule(), 'Themes');
        $this->setActionRights(1, $this->getModule(), 'UploadTheme');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'Overview',
            'extensions/modules',
            [
                'extensions/detail_module',
                'extensions/upload_module',
            ]
        );

        // Set navigation for "Settings > Themes"
        $navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes');
        $this->setNavigation(
            $navigationThemesId,
            'ThemesSelection',
            'extensions/themes',
            [
                'extensions/detail_theme',
                'extensions/upload_theme',
            ]
        );
        $this->setNavigation(
            $navigationThemesId,
            'Templates',
            'extensions/theme_templates',
            [
                'extensions/add_theme_template',
                'extensions/edit_theme_template',
            ]
        );
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->configureBackendActionRightsForModules();
        $this->configureBackendActionRightsForTemplates();
        $this->configureBackendActionRightsForThemes();
    }

    private function configureFrontendExtras(): void
    {
        $this->insertExtra('Search', ModuleExtraType::widget(), 'SearchForm', 'Form');
    }

    private function configureFrontendForkTheme(): void
    {
        // build templates
        $templates['fork']['default'] = [
            'theme' => 'Fork',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => true,
            'data' => serialize(
                [
                    'format' => '[/,/,top],[main,main,main]',
                    'image' => true,
                    'names' => ['main', 'top'],
                ]
            ),
        ];

        $templates['fork']['home'] = [
            'theme' => 'Fork',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => true,
            'data' => serialize(
                [
                    'format' => '[/,/,top],[main,main,main]',
                    'image' => true,
                    'names' => ['main', 'top'],
                ]
            ),
        ];

        // insert templates
        $this->getDatabase()->insert('themes_templates', $templates['fork']['default']);
        $this->getDatabase()->insert('themes_templates', $templates['fork']['home']);

        // @remark: custom for Sumocoders
        // Bootstrap templates

        // search will be installed by default; already link it to this template
        $extras['search_form'] = $this->insertExtra('search', ModuleExtraType::widget(), 'SearchForm', 'form', null, 'N', 2001);

        // build templates
        $templates['custom']['default'] = [
            'theme' => 'Custom',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => [
                        'main',
                        'top',
                    ],
                    'default_extras' => [
                        'top' => [
                            $extras['search_form'],
                        ],
                    ],
                ]
            ),
        ];

        $templates['custom']['error'] = [
            'theme' => 'Custom',
            'label' => 'Error',
            'path' => 'Core/Layout/Templates/Error.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => [
                        'main',
                        'top',
                    ],
                    'default_extras' => [
                        'top' => [
                            $extras['search_form'],
                        ],
                    ],
                ]
            ),
        ];

        $templates['custom']['home'] = [
            'theme' => 'Custom',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => [
                        'main',
                        'top',
                    ],
                    'default_extras' => [
                        'top' => [
                            $extras['search_form'],
                        ],
                    ],
                ]
            ),
        ];

        // insert templates
        $this->getDatabase()->insert('themes_templates', $templates['custom']['default']);
        $this->getDatabase()->insert('themes_templates', $templates['custom']['home']);
        $this->getDatabase()->insert('themes_templates', $templates['custom']['error']);

        /*
         * General theme settings
         */

        // set the theme
        $this->setSetting('Core', 'theme', 'Custom', true);

        // set default template
        $this->setSetting('Pages', 'default_template', $this->getTemplateId('Default'));

        // disable meta navigation
        $this->setSetting('Pages', 'meta_navigation', false);
    }
}
