<?php

/**
 * This is the detail-action
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class FrontendMailmotorDetail extends FrontendBaseBlock
{
	/**
	 * The mailing's body to assign in the template
	 *
	 * @var string
	 */
	private $body;

	/**
	 * Whether this is CM requesting the information
	 *
	 * @var	bool
	 */
	private $forCM = false;

	/**
	 * The ID of the mailing record in our database
	 *
	 * @var	int
	 */
	private $id;

	/**
	 * @var	array
	 */
	private $mailing;

	/**
	 * The type of content to base the body on.
	 *
	 * @var	string can be 'html' or 'plain'
	 */
	private $type;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();

		// overwrite the template path
		$this->setOverwrite(true);
		$this->setTemplatePath(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/templates/detail.tpl');

		$this->loadData();
		$this->parse();
	}

	/**
	 * @return string The generated mailing body.
	 */
	protected function getEmailBody()
	{
		$template = FrontendMailmotorModel::getTemplate(
			$this->mailing['language'],
			$this->mailing['template']
		);

		// define the key/value replacements to assign in to the mailing body
		$replacements = array(
			'{$siteURL}' => SITE_URL,
			'src="/"' => 'src="' . SITE_URL . '/'
		);

		// build the mailing body
		$mailing = new MailingBodyBuilder();
		$mailing->setTemplateContent($template['content']);
		$mailing->setEditorContent($this->mailing['content_html']);
		$mailing->setCSS($template['css']);
		$mailing->setUTMParameters($this->mailing['name']);

		if($this->type === 'plain')
		{
			$mailing->enablePlaintext();
		}

		$body = $mailing->buildBody($replacements);

		// Handle the online preview link
		if(preg_match_all('/<a.*?id="onlineVersionURL".*?>.*?<\/a>/', $body, $matches))
		{
			// loop the matches
			foreach($matches[0] as $match)
			{
				// replace the match
				$body = str_replace('href="#', 'href="' . FrontendMailmotorModel::getMailingPreviewURL($this->id, $this->type), $body);
			}
		}

		// Handle CM-related content substitutions, such as the unsubscribe link
		if($this->forCM)
		{
			// replace the unsubscribe
			if(preg_match_all('/<a.*?id="unsubscribeURL".*?>.*?<\/a>/', $body, $matches))
			{
				// loop the matches
				foreach($matches[0] as $match)
				{
					// get style attribute if one is provided
					preg_match('/style=".*?"/is', $match, $styleAttribute);

					// replace the match
					$body = str_replace($match, '<unsubscribe' . (isset($styleAttribute[0]) ? ' ' . $styleAttribute[0] : '') . '>' . strip_tags($match) . '</unsubscribe>', $body);
				}
			}
		}

		return $body;
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	protected function loadData()
	{
		$this->id = $this->URL->getParameter(1);
		$this->mailing = FrontendMailmotorModel::get($this->id);
		$this->type = SpoonFilter::getGetValue('type', array('html', 'plain'), 'html');
		$this->forCM = SpoonFilter::getGetValue('cm', array(0, 1), 0, 'bool');

		// no point continueing if the mailing record is not set
		if(empty($this->mailing))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}
	}

	/**
	 * Assigns the mailing body and other data into the template.
	 */
	protected function parse()
	{
		$this->breadcrumb->addElement($this->mailing['name']);
		$this->header->setPageTitle($this->mailing['name']);
		$this->tpl->assign('hideContentTitle', true);
		$this->tpl->assign('body', $this->getEmailBody());
	}
}
