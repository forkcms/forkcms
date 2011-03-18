<?php

/**
 * This is the Ical for a specific item
 *
 * @package		frontend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
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
	 *
	 * @return	void
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
		$icalItem = new FrontendIcalItemEvent($title, $this->record['full_url'], $description);

		// set dates
		$icalItem->setDatetimeStart(gmdate('U', $this->record['starts_on']));
		if($this->record['ends_on'] != null) $icalItem->setDatetimeEnd(gmdate('U', $this->record['ends_on']));
		$icalItem->setDatetimeCreated($this->record['created_on']);
		$icalItem->setDatetimeLastModified($this->record['edited_on']);

		// set other properties
		$icalItem->setCategories(array($this->record['category_title']));

		// add item
		$ical->addItem($icalItem);

		// output
		$ical->parse();
	}
}

?>