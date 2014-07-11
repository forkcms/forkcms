<?php

namespace Backend\Modules\Analytics\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Cronjob as BackendBaseCronjob;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Helper as BackendAnalyticsHelper;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This cronjob will fetch the traffic sources
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class GetTrafficSources extends BackendBaseCronjob
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // fork is no longer authorized to collect analytics data
        if (BackendAnalyticsHelper::getStatus() == 'UNAUTHORIZED') {
            // remove all parameters from the module settings
            BackendModel::setModuleSetting('Analytics', 'session_token', null);
            BackendModel::setModuleSetting('Analytics', 'account_name', null);
            BackendModel::setModuleSetting('Analytics', 'table_id', null);
            BackendModel::setModuleSetting('Analytics', 'profile_title', null);

            BackendAnalyticsModel::removeCacheFiles();
            BackendAnalyticsModel::clearTables();
            return;
        }

        $this->getData();
    }

    /**
     * Get data
     */
    private function getData()
    {
        try {
            BackendAnalyticsHelper::getRecentReferrers();
            BackendAnalyticsHelper::getRecentKeywords();
        } catch (\Exception $e) {
            throw new \SpoonException('Something went wrong while getting dashboard data.');
        }
    }
}
