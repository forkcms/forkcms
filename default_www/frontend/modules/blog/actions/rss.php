<?php

/**
 * FrontendBlogRss
 * This is the RSS-feed
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogRss extends FrontendBaseBlock
{
	/**
	 * The articles
	 *
	 * @var	array
	 */
	private $articles;


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
		$this->articles = FrontendBlogModel::getAll(30);

		// get settings
		$this->settings = FrontendModel::getModuleSettings('blog');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// get vars
		$title = (isset($this->settings['rss_title_'. FRONTEND_LANGUAGE])) ? $this->settings['rss_title_'. FRONTEND_LANGUAGE] : FrontendModel::getModuleSetting('blog', 'rss_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
		$link = SITE_URL . FrontendNavigation::getURLForBlock('blog');
		$description = (isset($this->settings['rss_description_'. FRONTEND_LANGUAGE])) ? $this->settings['rss_description_'. FRONTEND_LANGUAGE] : null;

		// create new rss instance
		$rss = new FrontendRSS($title, $link, $description);

		// loop articles
		foreach($this->articles as $item)
		{
			// init vars
			$title = $item['title'];
			$link = $item['full_url'];
			$description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];;

			// append meta
			$description .= '<div class="meta">'."\n";
			$description .= '	<p><a href="'. $link .'" title="'. $title .'">'. $title .'</a> ' . sprintf(FL::getMessage('WroteBy'), FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname'));
			$description .= ' '. FL::getLabel('In') .' <a href="'. $item['category_full_url'] .'" title="'. $item['category_name'] .'">'. $item['category_name'] .'</a>.</p>'."\n";

			// any tags
			if(isset($item['tags']))
			{
				// append tags-paragraph
				$description .= '	<p>'. ucfirst(FL::getLabel('Tags')) .': ';
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

			// create new instance
			$rssItem = new FrontendRSSItem($title, $link, $description);

			// set item properties
			$rssItem->setPublicationDate($item['publish_on']);
			$rssItem->addCategory($item['category_name']);
			$rssItem->setAuthor(FrontendUser::getBackendUser($item['user_id'])->getSetting('nickname'));

			// add item
			$rss->addItem($rssItem);
		}

		// output
		$rss->parse();
	}
}

?>