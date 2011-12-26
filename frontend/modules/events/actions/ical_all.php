<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Ical-feed
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsIcalAll extends FrontendBaseBlock
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $items;

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
		// get articles
		$this->items = FrontendEventsModel::getAll(30);

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

		// loop items
		foreach($this->items as $item)
		{
			// init vars
			$title = $item['title'];
			$description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];

			// create instance
			$icalItem = new FrontendIcalEvent($title, $item['full_url'], $description);

			// set dates
			$icalItem->setDTStamp(gmdate('U', $item['starts_on']));
			$icalItem->setStart(gmdate('U', $item['starts_on']));
			if($item['ends_on'] != null) $icalItem->setEnd(gmdate('U', $item['ends_on']));
			$icalItem->setCreated($item['created_on']);
			$icalItem->setLastModified($item['edited_on']);

			// set other properties
			$icalItem->addCategory($item['category_title']);

			// add item
			$ical->addEvent($icalItem);
		}

		// output
		$ical->parse();
	}
}
