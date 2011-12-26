<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Ical for a specific item
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsIcal extends FrontendBaseBlock
{
	/**
	 * The item
	 *
	 * @var	array
	 */
	private $record;

	/**
	 * The settings
	 *
	 * @var	array
	 */
	private $settings;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get by URL
		$this->record = FrontendEventsModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $this->record['url'];

		// get settings
		$this->settings = FrontendModel::getModuleSettings('events');
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// get vars
		$title = (isset($this->settings['ical_title_' . FRONTEND_LANGUAGE])) ? $this->settings['ical_title_' . FRONTEND_LANGUAGE] : FrontendModel::getModuleSetting('events', 'ical_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
		$description = (isset($this->settings['ical_description_' . FRONTEND_LANGUAGE])) ? $this->settings['ical_description_' . FRONTEND_LANGUAGE] : null;

		// create new ical instance
		$ical = new FrontendIcal($title, $description);

		// init vars
		$title = $this->record['title'];
		$description = ($this->record['introduction'] != '') ? $this->record['introduction'] : $this->record['text'];

		// create instance
		$icalItem = new FrontendIcalEvent($title, $this->record['full_url'], $description);

		// set dates
		$icalItem->setStart(gmdate('U', $this->record['starts_on']));
		if($this->record['ends_on'] != null) $icalItem->setEnd(gmdate('U', $this->record['ends_on']));
		$icalItem->setCreated($this->record['created_on']);
		$icalItem->setLastModified($this->record['edited_on']);

		// set other properties
		$icalItem->addCategory($this->record['category_title']);

		// add item
		$ical->addEvent($icalItem);

		// output
		$ical->parse();
	}
}
