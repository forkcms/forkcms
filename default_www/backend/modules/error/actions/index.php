<?php

/**
 * ErrorIndex
 *
 * This is the index-action (default), it will display an error depending on a given parameters
 *
 * @package		backend
 * @subpackage	error
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ErrorIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// parse the error
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse the correct messages into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// grab the error-type from the parameters
		$errorType = $this->getParameter('type');

		// set correct headers
		switch($errorType)
		{
			case 'not-allowed-module':
			case 'not-allowed-action':
				SpoonHTTP::setHeadersByCode(403);
			break;
		}


		// build the labelname
		$labelName = SpoonFilter::toCamelCase($errorType, '-');

		// assign the correct message into the template
		$this->tpl->assign('title', BackendLanguage::getMessage($labelName .'Title'));
		$this->tpl->assign('message', BackendLanguage::getMessage($labelName .'Message'));
	}
}
?>