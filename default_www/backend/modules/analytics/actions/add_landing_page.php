<?php

/**
 * This is the add-landing-page-action, it will display a form to create a new landing page
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsAddLandingPage extends BackendBaseActionAdd
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
	private $startTimestamp, $endTimestamp;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// set dates
		$this->setDates();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// get link list
		$this->linkList = BackendAnalyticsModel::getLinkList();

		// create elements
		$this->frm->addText('page_path');
		if(!empty($this->linkList))
		{
			$this->frm->addDropdown('page_list', $this->linkList);
			$this->frm->getField('page_list')->setDefaultElement('', 0);
		}
	}


	/**
	 * Set start and end timestamp needed to collect analytics data
	 *
	 * @return	void
	 */
	private function setDates()
	{
		// process
		BackendAnalyticsHelper::setDates();

		// get timestamps from session and set
		$this->startTimestamp = (int) SpoonSession::get('analytics_start_timestamp');
		$this->endTimestamp = (int) SpoonSession::get('analytics_end_timestamp');
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten values
			$pagePath = $this->frm->getField('page_path')->getValue();
			if(count($this->linkList) > 1) $pageList = $this->frm->getField('page_list')->getSelected();

			// get the target
			if($this->frm->getfield('page_path')->isFilled()) $page = $pagePath;
			elseif($pageList == '0') $page = null;
			else $page = (SITE_MULTILANGUAGE ? substr($pageList, strpos($pageList, '/', 1)) : $pageList);

			// validate fields
			if(isset($page) && !SpoonFilter::isURL(SITE_URL . $page)) $this->frm->getField('page_path')->addError(BL::err('InvalidURL'));
			if(!isset($page)) $this->frm->getField('page_path')->addError(BL::err('FieldIsRequired'));
			if(!$this->frm->getField('page_path')->isFilled() && !$this->frm->getfield('page_list')->isFilled()) $this->frm->getField('page_path')->addError(BL::err('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// get metrics
				$metrics = BackendAnalyticsHelper::getMetricsForPage($page, $this->startTimestamp, $this->endTimestamp);

				// build item
				$item['page_path'] = $page;
				$item['entrances'] = (isset($metrics['entrances']) ? $metrics['entrances'] : 0);
				$item['bounces'] = (isset($metrics['bounces']) ? $metrics['bounces'] : 0);
				$item['bounce_rate'] = ($metrics['entrances'] == 0 ? 0 : number_format(((int) $metrics['bounces'] / $metrics['entrances']) * 100, 2)) . '%';
				$item['start_date'] = date('Y-m-d', $this->startTimestamp) . ' 00:00:00';
				$item['end_date'] = date('Y-m-d', $this->endTimestamp) . ' 00:00:00';
				$item['updated_on'] = date('Y-m-d H:i:s');

				// insert the item
				$item['id'] = (int) BackendAnalyticsModel::insertLandingPage($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('landing_pages') . '&report=saved&var=' . urlencode($item['page_path']));
			}
		}
	}
}

?>