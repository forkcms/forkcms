<?php

namespace Backend\Modules\Extensions\Installer;

use Backend\Core\Installer\ModuleInstaller;

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
        $extras['search_form'] = $this->insertExtra('Search', 'widget', 'SearchForm', 'Form', null, 'N', 2001);
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
            'theme' => 'core',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                'format' => '[main]',
                'names' => array('main'),
            )),
        );

        $templates['core']['home'] = array(
            'theme' => 'core',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                'format' => '[main]',
                'names' => array('main'),
            )),
        );

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['core']['default']);
        $this->getDB()->insert('themes_templates', $templates['core']['home']);

        /*
         * Triton templates
         */

        // search will be installed by default; already link it to this template
        $extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

        // build templates
        $templates['triton']['default'] = array(
            'theme' => 'triton',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                'format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[left,main,main,main]',
                'names' => array('main', 'left', 'top', 'advertisement'),
                'default_extras' => array('top' => array($extras['search_form'])),
                'image' => false,
            )),
        );

        $templates['triton']['home'] = array(
            'theme' => 'triton',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                'format' => '[/,advertisement,advertisement,advertisement],[/,/,top,top],[/,/,/,/],[main,main,main,main],[left,left,right,right]',
                'names' => array('main', 'left', 'right', 'top', 'advertisement'),
                'default_extras' => array('top' => array($extras['search_form'])),
                'image' => true,
            )),
        );

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['triton']['default']);
        $this->getDB()->insert('themes_templates', $templates['triton']['home']);

        // @remark: custom for Sumocoders
        // Bootstrap templates

        // search will be installed by default; already link it to this template
        $extras['search_form'] = $this->insertExtra('search', 'widget', 'SearchForm', 'form', null, 'N', 2001);

        // build templates
        $templates['custom']['default'] = array(
            'theme' => 'Custom',
            'label' => 'Default',
            'path' => 'Core/Layout/Templates/Default.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => array('main', 'top'),
                    'default_extras' => array('top' => array($extras['search_form']))
                ))
        );

        $templates['custom']['error'] = array(
            'theme' => 'Custom',
            'label' => 'Error',
            'path' => 'Core/Layout/Templates/Error.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => array('main', 'top'),
                    'default_extras' => array('top' => array($extras['search_form']))
                ))
        );

        $templates['custom']['home'] = array(
            'theme' => 'Custom',
            'label' => 'Home',
            'path' => 'Core/Layout/Templates/Home.html.twig',
            'active' => 'Y',
            'data' => serialize(array(
                    'format' => '[/,/,/,top,/],[/,main,main,main,/]',
                    'names' => array('main', 'top'),
                    'default_extras' => array('top' => array($extras['search_form']))
                ))
        );

        // insert templates
        $this->getDB()->insert('themes_templates', $templates['custom']['default']);
        $this->getDB()->insert('themes_templates', $templates['custom']['home']);
        $this->getDB()->insert('themes_templates', $templates['custom']['error']);

        /*
         * General theme settings
         */

        // set default theme
        $this->setSetting('Core', 'theme', 'Custom', true);

        // set default template
        $this->setSetting('Pages', 'default_template', $this->getTemplateId('default'));

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
        $this->setNavigation($navigationModulesId, 'Overview', 'extensions/modules', array(
            'extensions/detail_module',
            'extensions/upload_module',
        ));

        // theme navigation
        $navigationThemesId = $this->setNavigation($navigationSettingsId, 'Themes');
        $this->setNavigation($navigationThemesId, 'ThemesSelection', 'extensions/themes', array(
            'extensions/upload_theme',
            'extensions/detail_theme',
        ));
        $this->setNavigation($navigationThemesId, 'Templates', 'extensions/theme_templates', array(
            'extensions/add_theme_template',
            'extensions/edit_theme_template',
        ));
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
}
