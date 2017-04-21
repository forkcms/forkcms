<?php

namespace Backend\Modules\Groups\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the groups module
 */
class Installer extends ModuleInstaller
{
    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertDashboardSequence()
    {
        $db = $this->getDB();

        // build groupsetting
        $groupSetting['group_id'] = 1;
        $groupSetting['name'] = 'dashboard_sequence';
        $groupSetting['value'] = serialize([]);

        // build usersetting
        $userSetting['user_id'] = 1;
        $userSetting['name'] = 'dashboard_sequence';
        $userSetting['value'] = serialize([]);

        // insert settings
        $db->insert('groups_settings', $groupSetting);
        $db->insert('users_settings', $userSetting);

        // insert default dashboard widget
        $this->insertDashboardWidget('Settings', 'Analyse');

        // insert default dashboard widget
        $this->insertDashboardWidget('Users', 'Statistics');
    }

    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add 'settings' as a module
        $this->addModule('Groups');

        // import locale
        $this->importLocale(__DIR__ . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, $this->getModule());

        // action rights
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Delete');

        // set navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $this->setNavigation($navigationSettingsId, 'Groups', 'groups/index', [
            'groups/add',
            'groups/edit',
        ], 5);

        // insert admins dashboard sequence
        $this->insertDashboardSequence();
    }
}
