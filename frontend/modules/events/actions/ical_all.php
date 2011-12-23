<?php

/**
 * This is the Ical-feed
 *
 * @package		frontend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
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
	 *
	 * @return	void
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
	 *
	 * @return	void
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
	 *
	 * @return	void
	 */
	private function parse()
	{
		// @todo	fix me
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
			$icalItem = new FrontendIcalItemEvent($title, $item['full_url'], $description);

			// set dates
			$icalItem->setDatetimeStart(gmdate('U', $item['starts_on']));
			if($item['ends_on'] != null) $icalItem->setDatetimeEnd(gmdate('U', $item['ends_on']));
			$icalItem->setDatetimeCreated($item['created_on']);
			$icalItem->setDatetimeLastModified($item['edited_on']);

			// set other properties
			$icalItem->setCategories(array($item['category_title']));

			// add item
			$ical->addItem($icalItem);
		}

		// output
		$ical->parse();
	}
}

?>