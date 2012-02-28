<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display an error depending on a given parameters
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class BackendErrorIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->parse();
		$this->display();
	}

	/**
	 * Parse the correct messages into the template
	 */
	protected function parse()
	{
		parent::parse();

		// grab the error-type from the parameters
		$errorType = $this->getParameter('type');

		// set correct headers
		switch($errorType)
		{
			case 'module-not-allowed':
			case 'action-not-allowed':
				SpoonHTTP::setHeadersByCode(403);
				break;

			case 'not-found':
				SpoonHTTP::setHeadersByCode(404);
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

				// give a nice error, so we can detect which file is missing
				echo 'Requested file (' . htmlspecialchars($this->getParameter('querystring')) . ') not found.';

				// stop script execution
				exit;
			}
		}

		// assign the correct message into the template
		$this->tpl->assign('message', BL::err(SpoonFilter::toCamelCase(htmlspecialchars($errorType), '-')));
	}
}
