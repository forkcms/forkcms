<?php

namespace Backend\Modules\Extensions\Installer;

use Backend\Core\Installer\ModuleInstaller;
use Common\ModuleExtraType;

/**
 * Installer for the extensions module.
 */
class Installer extends ModuleInstaller
{
    /**
     * Pre-insert default extras of the default theme.
     */
    private function insertExtras(): void
    {
        // insert extra ids
        $this->insertExtra('search', ModuleExtraType::widget(), 'SearchForm', 'form', null, false, 2001);
    }

    private function insertTemplates(): void
    {
        /*
         * Fallback templates
         */

        // build templates
        $templates = [];
        $templates['core']['default'] = [
            'theme' => 'Core',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[main]',
                    'names' => ['main'],
                ]
            ),
        ];

        $templates['core']['home'] = [
            'theme' => 'Core',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(
                [
                    'format' => '[main]',
                    'names' => ['main'],
                ]
            ),
        ];

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['core']['default']);
        $this->getDB()->insert('themes_templates', $templates['core']['home']);

        // search will be installed by default; already link it to this template
        $this->insertExtra('search', ModuleExtraType::widget(), 'SearchForm', 'form', null, false, 2001);

        /*
         * General theme settings
         */
        // set default template
        $this->setSetting('Pages', 'default_template', $this->getTemplateId('Default'));

        // disable meta navigation
        $this->setSetting('Pages', 'meta_navigation', false);
    }

    public function install(): void
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'extensions' as a module
        $this->addModule('Extensions');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // insert extras
        $this->insertExtras();

        // insert templates
        $this->insertTemplates();

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // set rights
        $this->setRights();

        // settings navigation
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

        // theme navigation
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

        if ($this->installExample()) {
            $this->installForkTheme();
        }
    }

    private function setRights(): void
    {
        // modules
        $this->setActionRights(1, $this->getModule(), 'Modules');
        $this->setActionRights(1, $this->getModule(), 'DetailModule');
        $this->setActionRights(1, $this->getModule(), 'InstallModule');
        $this->setActionRights(1, $this->getModule(), 'UploadModule');

        // themes
        $this->setActionRights(1, $this->getModule(), 'Themes');
        $this->setActionRights(1, $this->getModule(), 'DetailTheme');
        $this->setActionRights(1, $this->getModule(), 'InstallTheme');
        $this->setActionRights(1, $this->getModule(), 'UploadTheme');

        // templates
        $this->setActionRights(1, $this->getModule(), 'ThemeTemplates');
        $this->setActionRights(1, $this->getModule(), 'AddThemeTemplate');
        $this->setActionRights(1, $this->getModule(), 'EditThemeTemplate');
        $this->setActionRights(1, $this->getModule(), 'DeleteThemeTemplate');
    }

    private function installForkTheme(): void
    {
        /*
         * Fallback templates
         */

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
    }
}
