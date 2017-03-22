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
    private function insertExtras()
    {
        // insert extra ids
        $extras['search_form'] = $this->insertExtra('Search', ModuleExtraType::widget(), 'SearchForm', 'Form', null, 'N', 2001);
    }

    /**
     * Insert the templates.
     */
    private function insertTemplates()
    {
        /*
         * Fallback templates
         */

        // build templates
        $templates['core']['default'] = array(
            'theme' => 'Core',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(
                array(
                    'format' => '[main]',
                    'names' => array('main'),
                )
            ),
        );

        $templates['core']['home'] = array(
            'theme' => 'Core',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(
                array(
                    'format' => '[main]',
                    'names' => array('main'),
                )
            ),
        );

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['core']['default']);
        $this->getDB()->insert('themes_templates', $templates['core']['home']);

        // search will be installed by default; already link it to this template
        $extras['search_form'] = $this->insertExtra('search', ModuleExtraType::widget(), 'SearchForm', 'form', null, 'N', 2001);

        /*
         * General theme settings
         */
        // set default template
        $this->setSetting('Pages', 'default_template', $this->getTemplateId('Default'));

        // disable meta navigation
        $this->setSetting('Pages', 'meta_navigation', false);
    }

    /**
     * Install the module
     */
    public function install()
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
        $this->setModuleRights(1, 'Extensions');

        // set rights
        $this->setRights();

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'Overview',
            'extensions/modules',
            array(
                'extensions/detail_module',
                'extensions/upload_module',
            )
        );

        // theme navigation
        $navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes');
        $this->setNavigation(
            $navigationThemesId,
            'ThemesSelection',
            'extensions/themes',
            array(
                'extensions/upload_theme',
                'extensions/detail_theme',
            )
        );
        $this->setNavigation(
            $navigationThemesId,
            'Templates',
            'extensions/theme_templates',
            array(
                'extensions/add_theme_template',
                'extensions/edit_theme_template',
            )
        );

        if ($this->installExample()) {
            $this->installForkTheme();
        }
    }

    /**
     * Set the rights
     */
    private function setRights()
    {
        // modules
        $this->setActionRights(1, 'Extensions', 'Modules');
        $this->setActionRights(1, 'Extensions', 'DetailModule');
        $this->setActionRights(1, 'Extensions', 'InstallModule');
        $this->setActionRights(1, 'Extensions', 'UploadModule');

        // themes
        $this->setActionRights(1, 'Extensions', 'Themes');
        $this->setActionRights(1, 'Extensions', 'DetailTheme');
        $this->setActionRights(1, 'Extensions', 'InstallTheme');
        $this->setActionRights(1, 'Extensions', 'UploadTheme');

        // templates
        $this->setActionRights(1, 'Extensions', 'ThemeTemplates');
        $this->setActionRights(1, 'Extensions', 'AddThemeTemplate');
        $this->setActionRights(1, 'Extensions', 'EditThemeTemplate');
        $this->setActionRights(1, 'Extensions', 'DeleteThemeTemplate');
    }

    private function installForkTheme()
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
            )
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
                    'names' => array('main'),
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
