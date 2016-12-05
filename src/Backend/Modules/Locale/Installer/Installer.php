<?php

namespace Backend\Modules\Locale\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the locale module
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'locale' as a module
        $this->addModule('Locale');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // import core locale
        $this->importLocale(dirname(dirname(dirname(__DIR__))) . '/Core/Installer/Data/locale.xml');

        // import dashboard locale
        $this->importLocale(dirname(dirname(__DIR__)) . '/Dashboard/Installer/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Locale');

        // action rights
        $this->setActionRights(1, 'Locale', 'Add');
        $this->setActionRights(1, 'Locale', 'Analyse');
        $this->setActionRights(1, 'Locale', 'Edit');
        $this->setActionRights(1, 'Locale', 'ExportAnalyse');
        $this->setActionRights(1, 'Locale', 'Index');
        $this->setActionRights(1, 'Locale', 'SaveTranslation');
        $this->setActionRights(1, 'Locale', 'Export');
        $this->setActionRights(1, 'Locale', 'Import');
        $this->setActionRights(1, 'Locale', 'Delete');

        // set navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);
        $this->setNavigation($navigationSettingsId, 'Translations', 'locale/index', array(
            'locale/add',
            'locale/edit',
            'locale/import',
            'locale/analyse',
        ), 4);
    }
}
