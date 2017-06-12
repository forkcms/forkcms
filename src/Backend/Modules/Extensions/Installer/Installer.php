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
                'extensions/upload_theme',
                'extensions/detail_theme',
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
        $templates['core']['default'] = [
            'theme' => 'Fork',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[main]',
                    'image' => true,
                    'names' => ['main'],
                ]
            ),
        ];

        $templates['core']['home'] = [
            'theme' => 'Fork',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[main]',
                    'image' => true,
                    'names' => ['main'],
                ]
            ),
        ];

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['core']['default']);
        $this->getDB()->insert('themes_templates', $templates['core']['home']);

        // set the theme
        $this->setSetting('Core', 'theme', 'Fork', true);

        // set default template
        $this->setSetting('Pages', 'default_template', $this->getTemplateId('Default'));

        // disable meta navigation
        $this->setSetting('Pages', 'meta_navigation', false);
    }
}
