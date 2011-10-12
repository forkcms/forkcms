<?php

/**
 * This is the loading-action, it will display a spinner while data is collected
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsLoading extends BackendAnalyticsBase
{
	/**
	 * The page id of the page we are requesting
	 *
	 * @var	int
	 */
	private $pageId;


	/**
	 * The redirect action and identifier to give along with the curl call
	 *
	 * @var	string
	 */
	private $redirectAction, $identifier;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// call to get_data script
		$this->getData();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Call to the get_data script
	 *
	 * @return	void
	 */
	private function getData()
	{
		// init vars
		$this->redirectAction = SpoonFilter::getGetValue('redirect_action', null, 'index');
		$this->identifier = time() . rand(0, 999);
		$this->pageId = SpoonFilter::getGetValue('page_id', null, '');
		$this->pagePath = SpoonFilter::getGetValue('page_path', null, '');
		$force = SpoonFilter::getGetValue('force', null, '');

		// no id set but we have a path
		if($this->pageId == '' && $this->pagePath != '')
		{
			// get page for path
			$page = BackendAnalyticsModel::getPageByPath($this->pagePath);

			// set id
			$this->pageId = (isset($page['id']) ? $page['id'] : '');
		}

		// build url
		$URL = SITE_URL . '/backend/cronjob.php?module=analytics&action=get_data&id=1';
		$URL .= '&page=' . $this->redirectAction;
		if($this->pageId != '') $URL .= '&page_id=' . $this->pageId;
		$URL .= '&identifier=' . $this->identifier;
		$URL .= '&start_date=' . $this->startTimestamp;
		$URL .= '&end_date=' . $this->endTimestamp;
		$URL .= '&force=' . $force;

		// set options
		$options = array();
		$options[CURLOPT_URL] = $URL;
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = 1;

		// init
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		curl_exec($curl);

		// close
		curl_close($curl);
	}


	/**
	 * Parse this page
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// get page
		$page = ($this->pageId != '' ? BackendAnalyticsModel::getPageForId($this->pageId) : null);

		// update date_viewed for this page
		BackendAnalyticsModel::updatePageDateViewed($this->pageId);

		// parse redirect link
		$this->tpl->assign('redirect', BackendModel::createURLForAction($this->redirectAction));
		$this->tpl->assign('redirectGet', (isset($page) ? 'page=' . $page : ''));
		$this->tpl->assign('settingsUrl', BackendModel::createURLForAction('settings'));
		$this->tpl->assign('page', $this->redirectAction);
		$this->tpl->assign('identifier', ($this->pageId != '' ? $this->pageId . '_' : '') . $this->identifier);
	}
}

?>