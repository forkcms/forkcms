<?php

// create new instance
$app = new ApplicationRouting();

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package			Frontend
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			2.0
 */
class ApplicationRouting
{
	// Default application
	const DEFAULT_APPLICATION = 'frontend';


	/**
	 * Virtual folders mappings
	 *
	 * @var	array
	 */
	private $aRoutes = array('' => self::DEFAULT_APPLICATION,
							'private' => 'backend',
							'backend' => 'backend',
							'api' => 'api');


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// process querystring
		$this->processQueryString();

		// require correct app
		require_once APPLICATION .'/index.php';
	}


	/**
	 * Process the querystring to define the application
	 *
	 * @return	void
	 */
	private function processQueryString()
	{
		// get querystring
		$queryString = trim($_SERVER['REQUEST_URI'], '/');

		// split into chunks
		$aChunks = explode('/', $queryString);

		// is there a application specified
		if(isset($aChunks[0]))
		{
			// cleanup
			$proposedApplication = (string) $aChunks[0];

			// set real application
			$application = (isset($this->aRoutes[$proposedApplication])) ? $this->aRoutes[$proposedApplication] : self::DEFAULT_APPLICATION;
		}

		// no application
		else $application = self::DEFAULT_APPLICATION;

		// define APP
		define('APPLICATION', $application);
		define('NAMED_APPLICATION', $proposedApplication);
	}
}

?>