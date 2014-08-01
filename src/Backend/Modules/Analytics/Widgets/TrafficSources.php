<?php

namespace Backend\Modules\Analytics\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This widget will show the latest traffic sources
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class TrafficSources extends BackendBaseWidget
{
    /**
     * Execute the widget
     */
    public function execute()
    {
        // check analytics session token and analytics table id
        if (
            BackendModel::getModuleSetting('Analytics', 'session_token', null) == '' ||
            BackendModel::getModuleSetting('Analytics', 'table_id', null) == ''
        ) {
            return;
        }

        // settings are ok, set option
        $this->tpl->assign('analyticsValidSettings', true);

        $this->setColumn('left');
        $this->setPosition(0);
        $this->header->addJS('Dashboard.js', 'Analytics');
        $this->parse();
        $this->getData();
        $this->display();
    }

    /**
     * Parse into template
     */
    private function getData()
    {
        $URL = SITE_URL . '/backend/cronjob?module=Analytics&action=GetTrafficSources&id=2';

        // set options
        $options = array();
        $options[CURLOPT_URL] = $URL;
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = 1;

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Parse into template
     */
    private function parse()
    {
        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Settings', 'Analytics')) {
            // parse redirect link
            $this->tpl->assign('settingsUrl', BackendModel::createURLForAction('Settings', 'Analytics'));
        }

        $this->parseKeywords();
        $this->parseReferrers();
    }

    /**
     * Parse the keywords datagrid
     */
    private function parseKeywords()
    {
        $results = BackendAnalyticsModel::getRecentKeywords();
        if (!empty($results)) {
            $dataGrid = new BackendDataGridArray($results);
            $dataGrid->setPaging(false);
            $dataGrid->setColumnsHidden('id', 'date');

            // parse the datagrid
            $this->tpl->assign('dgAnalyticsKeywords', $dataGrid->getContent());
        }

        // get date
        $date = (isset($results[0]['date']) ? substr($results[0]['date'], 0, 10) : date('Y-m-d'));
        $timestamp = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

        // assign date label
        $this->tpl->assign('analyticsTrafficSourcesDate', ($date != date('Y-m-d') ? BackendModel::getUTCDate('d-m', $timestamp) : BL::lbl('Today')));
    }

    /**
     * Parse the referrers datagrid
     */
    private function parseReferrers()
    {
        $results = BackendAnalyticsModel::getRecentReferrers();
        if (!empty($results)) {
            $dataGrid = new BackendDataGridArray($results);
            $dataGrid->setPaging(false);
            $dataGrid->setColumnsHidden('id', 'date', 'url');
            $dataGrid->setColumnURL('referrer', '[url]');

            // parse the datagrid
            $this->tpl->assign('dgAnalyticsReferrers', $dataGrid->getContent());
        }
    }
}
