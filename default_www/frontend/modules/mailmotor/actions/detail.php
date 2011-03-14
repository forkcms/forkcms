<?php

/**
 * FrontendMailmotorDetail
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class FrontendMailmotorDetail extends FrontendBaseBlock
{
	/**
	 * stores whether this is CM requesting the information
	 *
	 * @var	bool
	 */
	private $forCM = false;


	/**
	 * The ID of the mailing
	 *
	 * @var	int
	 */
	private $id;


	/**
	 * The mailing
	 *
	 * @var	array
	 */
	private $record;


	/**
	 * The type of content to show (HTML or plain)
	 *
	 * @var	string
	 */
	private $type;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

		// load the data
		$this->getData();

		// overwrite the template path
		$this->setOverwrite(true);
		$this->setTemplatePath(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/templates/detail.tpl');

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
		// store the ID
		$this->id = $this->URL->getParameter(1);

		// store the type
		$this->type = SpoonFilter::getGetValue('type', array('html', 'plain'), 'html');

		// is this CM asking the info?
		$this->forCM = SpoonFilter::getGetValue('cm', array(0, 1), 0, 'bool');

		// fetch the mailing data
		$this->record = FrontendMailmotorModel::get($this->id);

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['name']);

		// set meta
		$this->header->setPageTitle($this->record['name']);

		// set the content to parse
		$content = ($this->type == 'html') ? $this->record['data']['full_content_html'] : $this->record['content_plain'];

		// cm is asking the info
		if($this->forCM)
		{
			// replace the unsubscribe
			if(preg_match_all('/<a id="unsubscribeURL".*?>.*?<\/a>/is', $content, $matches))
			{
				// loop the matches
				foreach($matches[0] as $match)
				{
					// replace the match
					$content = str_replace($match, '<unsubscribe>' . strip_tags($match) . '</unsubscribe>', $content);
				}
			}

			// online preview links
			if(preg_match_all('/<a id="onlineVersionURL".*?>.*?<\/a>/is', $content, $matches))
			{
				// loop the matches
				foreach($matches[0] as $match)
				{
					// replace the match
					$content = str_replace('href="#', 'href="' . FrontendMailmotorModel::getMailingPreviewURL($this->id, $this->type), $content);
				}
			}
		}

		// assign article
		$this->tpl->assign('mailingContent', $content);
	}
}

?>