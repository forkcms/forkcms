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
        $this->importLocale(dirname(__DIR__, 3) . '/Core/Installer/Data/locale.xml');

        // import dashboard locale
        $this->importLocale(dirname(__DIR__, 2) . '/Dashboard/Installer/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Analyse');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'ExportAnalyse');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'SaveTranslation');
        $this->setActionRights(1, $this->getModule(), 'Export');
        $this->setActionRights(1, $this->getModule(), 'Import');
        $this->setActionRights(1, $this->getModule(), 'Delete');

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
