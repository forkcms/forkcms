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
 *
 * @author Jeroen Van den Bossche <jeroenvandenbossche@netlash.com>
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
        $groupSetting['value'] = serialize(array());

        // build usersetting
        $userSetting['user_id'] = 1;
        $userSetting['name'] = 'dashboard_sequence';
        $userSetting['value'] = serialize(array());

        // insert settings
        $db->insert('groups_settings', $groupSetting);
        $db->insert('users_settings', $userSetting);

        // create default dashboard widget
        $analyse = array(
            'column' => 'left',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        // insert default dashboard widget
        $this->insertDashboardWidget('Settings', 'Analyse', $analyse);

        // create default dashboard widget
        $statistics = array(
            'column' => 'left',
            'position' => 2,
            'hidden' => false,
            'present' => true
        );

        // insert default dashboard widget
        $this->insertDashboardWidget('Users', 'Statistics', $statistics);
    }

    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'settings' as a module
        $this->addModule('Groups');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Groups');

        // action rights
        $this->setActionRights(1, 'Groups', 'Index');
        $this->setActionRights(1, 'Groups', 'Add');
        $this->setActionRights(1, 'Groups', 'Edit');
        $this->setActionRights(1, 'Groups', 'Delete');

        // set navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $this->setNavigation($navigationSettingsId, 'Groups', 'groups/index', array(
            'groups/add',
            'groups/edit'
        ), 5);

        // insert admins dashboard sequence
        $this->insertDashboardSequence();
    }
}
