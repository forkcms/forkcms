<?php

namespace Backend\Modules\Analytics\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;
use Backend\Modules\Analytics\Engine\Base as BackendAnalyticsBase;

/**
 * This is the loading-action, it will display a spinner while data is collected
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class Loading extends BackendAnalyticsBase
{
    /**
     * The page id of the page we are requesting
     *
     * @var	int
     */
    private $pageId;

    /**
     * The redirect action and identifier to give along with the curl call
     *
     * @var	string
     */
    private $redirectAction;
    private $identifier;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->parse();
        $this->display();
    }

    /**
     * Call to the get_data script
     */
    private function getData()
    {
        $this->redirectAction = \SpoonFilter::getGetValue('redirect_action', null, 'Index');
        $this->identifier = time() . rand(0, 999);
        $this->pageId = \SpoonFilter::getGetValue('page_id', null, '');
        $this->pagePath = \SpoonFilter::getGetValue('page_path', null, '');
        $force = \SpoonFilter::getGetValue('force', null, '');

        // no id set but we have a path
        if ($this->pageId == '' && $this->pagePath != '') {
            // get page for path
            $page = BackendAnalyticsModel::getPageByPath($this->pagePath);

            // set id
            $this->pageId = (isset($page['id']) ? $page['id'] : '');
        }

        // build url
        $URL = SITE_URL . '/backend/cronjob?module=Analytics&action=GetData&id=1';
        $URL .= '&page=' . $this->redirectAction;
        if ($this->pageId != '') {
            $URL .= '&page_id=' . $this->pageId;
        }
        $URL .= '&identifier=' . $this->identifier;
        $URL .= '&start_date=' . $this->startTimestamp;
        $URL .= '&end_date=' . $this->endTimestamp;
        $URL .= '&force=' . $force;

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
     * Parse this page
     */
    protected function parse()
    {
        parent::parse();

        $page = ($this->pageId != '' ? BackendAnalyticsModel::getPageForId($this->pageId) : null);

        // update date_viewed for this page
        BackendAnalyticsModel::updatePageDateViewed($this->pageId);

        $data = array();
        $data['redirectGet'] = (isset($page) ? 'page=' . $page : '');
        $data['page'] = $this->redirectAction;
        $data['identifier'] = ($this->pageId != '' ? $this->pageId . '_' : '') . $this->identifier;

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction($this->redirectAction, $this->getModule())) {
            $data['redirect'] = BackendModel::createURLForAction($this->redirectAction);
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Settings', $this->getModule())) {
            $data['settingsUrl'] = BackendModel::createURLForAction('Settings');
        }

        // parse redirect link
        $this->header->addJsData('analytics', 'data', $data);
    }
}
