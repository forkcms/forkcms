<?php

namespace Backend\Modules\Settings\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the settings module
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // add 'settings' as a module
        $this->addModule('Settings');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Settings');

        // action rights
        $this->setActionRights(1, 'Settings', 'Index');
        $this->setActionRights(1, 'Settings', 'Email');
        $this->setActionRights(1, 'Settings', 'Seo');
        $this->setActionRights(1, 'Settings', 'TestEmailConnection');

        // set navigation (settings should be last tab)
        $navigationSettingsId = $this->setNavigation(null, 'Settings', null, null, 999);

        // general navigation
        $this->setNavigation($navigationSettingsId, 'General', 'settings/index', null, 1);
        $navigationAdvancedId = $this->setNavigation($navigationSettingsId, 'Advanced', null, null, 2);
        $this->setNavigation($navigationAdvancedId, 'Email', 'settings/email');
        $this->setNavigation($navigationAdvancedId, 'SEO', 'settings/seo');

        // modules settings navigation
        $this->setNavigation($navigationSettingsId, 'Modules', null, null, 6);

        // themes settings navigation
        $this->setNavigation($navigationSettingsId, 'Themes', null, null, 7);
    }
}
