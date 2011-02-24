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
		// get vars
		$title = (isset($this->settings['ical_title_'. FRONTEND_LANGUAGE])) ? $this->settings['ical_title_'. FRONTEND_LANGUAGE] : FrontendModel::getModuleSetting('events', 'ical_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
		$link = SITE_URL . FrontendNavigation::getURLForBlock('events');
		$description = (isset($this->settings['ical_description_'. FRONTEND_LANGUAGE])) ? $this->settings['ical_description_'. FRONTEND_LANGUAGE] : null;

		// create new ical instance
		$ical = new FrontendIcal($title, $link, $description);

		// loop articles
		foreach($this->items as $item)
		{
			// init vars
			$title = SpoonDate::getDate(FrontendModel::getModuleSetting('core', 'date_format_short'), $item['starts_on'], FRONTEND_LANGUAGE) .' - '. $item['title'];
			$link = $item['full_url'];
			$description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];

			// meta is wanted
			if(FrontendModel::getModuleSetting('events', 'ical_meta_'. FRONTEND_LANGUAGE, true))
			{
				// append meta
				$description .= '<div class="meta">'."\n";
				$description .= '	<p><a href="'. $link .'" title="'. $title .'">'. $title .'</a> ' . sprintf(FL::msg('WrittenBy'), FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname'));
				$description .= ' '. FL::lbl('In') .' <a href="'. $item['category_full_url'] .'" title="'. $item['category_name'] .'">'. $item['category_name'] .'</a>.</p>'."\n";

				// any tags
				if(isset($item['tags']))
				{
					// append tags-paragraph
					$description .= '	<p>'. ucfirst(FL::lbl('Tags')) .': ';
					$first = true;

					// loop tags
					foreach($item['tags'] as $tag)
					{
						// prepend separator
						if(!$first) $description .= ', ';

						// add
						$description .= '<a href="'. $tag['full_url'] .'" rel="tag" title="'. $tag['name'] .'">'. $tag['name'] .'</a>';

						// reset
						$first = false;
					}

					// end
					$description .= '.</p>'."\n";
				}

				// end HTML
				$description .= '</div>'."\n";
			}

			// create new instance
			$icalItem = new FrontendIcalItem($title, $link, $description);

			// set item properties
			$icalItem->setPublicationDate($item['publish_on']);
			$icalItem->addCategory($item['category_name']);
			$icalItem->setAuthor(FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname'));

			// add item
			$ical->addItem($icalItem);
		}

		// output
		$ical->parse();
	}
}

?>