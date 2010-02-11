<?php

/**
 * BackendErrorIndex
 * This is the index-action (default), it will display an error depending on a given parameters
 *
 * @todo	if the session is expired their should be a redirect to the login form instead of the forbidden messages.
 *
 * @package		backend
 * @subpackage	error
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendErrorIndex extends BackendBaseActionIndex
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

		// querystring provided?
		if($this->getParameter('querystring') !== null)
		{
			// split into file and parameters
			$chunks = explode('?', $this->getParameter('querystring'));

			// get extension
			$extension = SpoonFile::getExtension($chunks[0]);

			// if the file has an extension it is a non-existing-file
			if($extension != '' && $extension != $chunks[0])
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(404);

				// stop the script, but give a nice error, so we can detect which file is missing
				exit('Requested file ('. implode('?', $chunks) .') not found.');
			}
		}

		// build the labelname
		$labelName = SpoonFilter::toCamelCase($errorType, '-');

		// assign the correct message into the template
		$this->tpl->assign('title', BackendLanguage::getMessage($labelName .'Title'));
		$this->tpl->assign('message', BackendLanguage::getMessage($labelName .'Message'));
	}
}

?>