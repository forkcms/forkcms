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
    public function install(): void
    {
        $this->addModule('Groups');
        $this->importSQL(__DIR__ . '/Data/install.sql');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $this->setNavigation($navigationSettingsId, 'Groups', 'groups/index', [
            'groups/add',
            'groups/edit',
        ], 5);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureBackendWidgets(): void
    {
        $db = $this->getDB();

        // build groupsetting
        $groupSetting = [];
        $groupSetting['group_id'] = 1;
        $groupSetting['name'] = 'dashboard_sequence';
        $groupSetting['value'] = serialize([]);

        // build usersetting
        $userSetting = [];
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
}
