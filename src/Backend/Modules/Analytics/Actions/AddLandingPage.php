<?php

namespace Backend\Modules\Analytics\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Analytics\Engine\Helper as BackendAnalyticsHelper;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This is the add-landing-page-action, it will display a form to create a new landing page
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class AddLandingPage extends BackendBaseActionAdd
{
    /**
     * The list of links in the application
     *
     * @var	array
     */
    private $linkList = array();

    /**
     * The start and end timestamp of the collected data
     *
     * @var	int
     */
    private $startTimestamp;
    private $endTimestamp;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->setDates();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('add');

        // get link list
        $this->linkList = BackendAnalyticsModel::getLinkList();

        // create elements
        $this->frm->addText('page_path');
        if (!empty($this->linkList)) {
            $this->frm->addDropdown('page_list', $this->linkList);
            $this->frm->getField('page_list')->setDefaultElement('', 0);
        }
    }

    /**
     * Set start and end timestamp needed to collect analytics data
     */
    private function setDates()
    {
        BackendAnalyticsHelper::setDates();

        $this->startTimestamp = (int) \SpoonSession::get('analytics_start_timestamp');
        $this->endTimestamp = (int) \SpoonSession::get('analytics_end_timestamp');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // shorten values
            $pagePath = $this->frm->getField('page_path')->getValue();
            if (count($this->linkList) > 1) {
                $pageList = $this->frm->getField('page_list')->getSelected();
            }

            // get the target
            if ($this->frm->getfield('page_path')->isFilled()) {
                $page = $pagePath;
            } elseif ($pageList == '0') {
                $page = null;
            } else {
                $page = (SITE_MULTILANGUAGE ? substr($pageList, strpos($pageList, '/', 1)) : $pageList);
            }

            // validate fields
            if (isset($page) && !\SpoonFilter::isURL(SITE_URL . $page)) {
                $this->frm->getField('page_path')->addError(BL::err('InvalidURL'));
            }
            if (!isset($page)) {
                $this->frm->getField('page_path')->addError(BL::err('FieldIsRequired'));
            }
            if (!$this->frm->getField('page_path')->isFilled() && !$this->frm->getfield('page_list')->isFilled()) {
                $this->frm->getField('page_path')->addError(BL::err('FieldIsRequired'));
            }

            if ($this->frm->isCorrect()) {
                // get metrics
                $metrics = BackendAnalyticsHelper::getMetricsForPage($page, $this->startTimestamp, $this->endTimestamp);

                // build item
                $item['page_path'] = $page;
                $item['entrances'] = (isset($metrics['entrances']) ? $metrics['entrances'] : 0);
                $item['bounces'] = (isset($metrics['bounces']) ? $metrics['bounces'] : 0);
                $item['bounce_rate'] = ($metrics['entrances'] == 0 ?
                    0 :
                    number_format(((int) $metrics['bounces'] / $metrics['entrances']) * 100, 2)) . '%'
                ;
                $item['start_date'] = date('Y-m-d', $this->startTimestamp) . ' 00:00:00';
                $item['end_date'] = date('Y-m-d', $this->endTimestamp) . ' 00:00:00';
                $item['updated_on'] = date('Y-m-d H:i:s');

                // insert the item
                $item['id'] = (int) BackendAnalyticsModel::insertLandingPage($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_landing_page', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('LandingPages') .
                    '&report=saved&var=' . urlencode($item['page_path'])
                );
            }
        }
    }
}
