<?php

namespace Backend\Modules\Analytics\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Modules\Analytics\Engine\Helper as BackendAnalyticsHelper;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This edit-action will refresh the traffic sources using Ajax
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class RefreshTrafficSources extends BackendBaseAJAXAction
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
            BackendModel::setModuleSetting($this->getModule(), 'session_token', null);
            BackendModel::setModuleSetting($this->getModule(), 'account_name', null);
            BackendModel::setModuleSetting($this->getModule(), 'table_id', null);
            BackendModel::setModuleSetting($this->getModule(), 'profile_title', null);

            BackendAnalyticsModel::removeCacheFiles();
            BackendAnalyticsModel::clearTables();

            $this->output(
                static::OK,
                array('status' => 'unauthorized', 'message' => BL::msg('Redirecting')),
                'No longer authorized.'
            );
        } else {
            // authorized: get data
            $this->getData();

            // get html
            $referrersHtml = $this->parseReferrers();
            $keywordsHtml = $this->parseKeywords();

            // return status
            $this->output(
                static::OK,
                array(
                    'status' => 'success',
                    'referrersHtml' => $referrersHtml,
                    'keywordsHtml' => $keywordsHtml,
                    'date' => BL::lbl('Today'),
                    'message' => BL::msg('RefreshedTrafficSources')
                ),
                'Data has been retrieved.'
            );
        }
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
            $this->output(static::OK, array('status' => 'error'), 'Something went wrong while getting traffic sources.');
        }
    }

    /**
     * Parse into template
     */
    private function parseKeywords()
    {
        $results = BackendAnalyticsModel::getRecentKeywords();
        if (!empty($results)) {
            $dataGrid = new BackendDataGridArray($results);
            $dataGrid->setPaging(false);
            $dataGrid->setColumnsHidden('id', 'date');
        }

        // parse the datagrid
        return (!empty($results) ?
            $dataGrid->getContent() :
            '<table class="dataGrid"><tr><td>' . BL::msg('NoKeywords') . '</td></tr></table>')
        ;
    }

    /**
     * Parse into template
     */
    private function parseReferrers()
    {
        $results = BackendAnalyticsModel::getRecentReferrers();
        if (!empty($results)) {
            $dataGrid = new BackendDataGridArray($results);
            $dataGrid->setPaging();
            $dataGrid->setColumnsHidden('id', 'date', 'url');
            $dataGrid->setColumnURL('referrer', '[url]');
        }

        // parse the datagrid
        return (!empty($results) ?
            $dataGrid->getContent() :
            '<table class="dataGrid"><tr><td>' . BL::msg('NoReferrers') . '</td></tr></table>'
        );
    }
}
