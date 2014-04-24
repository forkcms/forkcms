<?php

namespace Backend\Modules\Analytics\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the analytics module
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class Installer extends ModuleInstaller
{
    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidgets()
    {
        $trafficSources = array(
            'column' => 'middle',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        $visitors = array(
            'column' => 'middle',
            'position' => 2,
            'hidden' => false,
            'present' => true
        );

        // insert widgets
        $this->insertDashboardWidget('Analytics', 'TrafficSources', $trafficSources);
        $this->insertDashboardWidget('Analytics', 'Visitors', $visitors);
    }

    /**
     * Install the module
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'analytics' as a module
        $this->addModule('Analytics');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Analytics');

        // action rights
        $this->setActionRights(1, 'Analytics', 'AddLandingPage');
        $this->setActionRights(1, 'Analytics', 'AllPages');
        $this->setActionRights(1, 'Analytics', 'CheckStatus');
        $this->setActionRights(1, 'Analytics', 'Content');
        $this->setActionRights(1, 'Analytics', 'DeleteLandingPage');
        $this->setActionRights(1, 'Analytics', 'DetailPage');
        $this->setActionRights(1, 'Analytics', 'ExitPages');
        $this->setActionRights(1, 'Analytics', 'GetTrafficSources');
        $this->setActionRights(1, 'Analytics', 'Index');
        $this->setActionRights(1, 'Analytics', 'LandingPages');
        $this->setActionRights(1, 'Analytics', 'Loading');
        $this->setActionRights(1, 'Analytics', 'MassLandingPageAction');
        $this->setActionRights(1, 'Analytics', 'RefreshTrafficSources');
        $this->setActionRights(1, 'Analytics', 'Settings');
        $this->setActionRights(1, 'Analytics', 'TrafficSources');
        $this->setActionRights(1, 'Analytics', 'Visitors');

        // set navigation
        $navigationMarketingId = $this->setNavigation(null, 'Marketing', 'analytics/index', null, 4);
        $navigationAnalyticsId = $this->setNavigation($navigationMarketingId, 'Analytics', 'analytics/index', array('analytics/loading'));
        $this->setNavigation($navigationAnalyticsId, 'Content', 'analytics/content');
        $this->setNavigation($navigationAnalyticsId, 'AllPages', 'analytics/all_pages');
        $this->setNavigation($navigationAnalyticsId, 'ExitPages', 'analytics/exit_pages');
        $this->setNavigation($navigationAnalyticsId, 'LandingPages', 'analytics/landing_pages', array(
            'analytics/add_landing_page',
            'analytics/edit_landing_page',
            'analytics/detail_page'
        ));

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Analytics', 'analytics/settings');

        // insert dashboard widgets
        $this->insertWidgets();
    }
}
