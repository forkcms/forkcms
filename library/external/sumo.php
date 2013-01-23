<?php

/**
 * Our Sumo class with specific Sumo stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Sumo
{
	/**
	 * Default constructor
	 */
	public function __construct()
	{
		if(!SPOON_DEBUG) $this->initErrbit();
	}

	/**
	 * This errorhandler will push the error to Errbit
	 *
	 * @param object $exception The exception that was thrown.
	 * @param string $output The output that should be mailed.
	 */
	public static function exceptionHandler($exception, $output)
	{
		Errbit::instance()->notify($exception);

		if(APPLICATION == 'frontend') FrontendInit::exceptionHandler($exception, $output);
		elseif(APPLICATION == 'backend') BackendInit::exceptionHandler($exception, $output);
		elseif(APPLICATION == 'api') APIInit::exceptionHandler($exception, $output);
	}

	/**
	 * Initialize Errbit
	 */
	public function initErrbit()
	{
		// only initialize if we know the API key
		if(!defined('ERRBIT_API_KEY') && ERRBIT_API_KEY != '') return;

		require_once 'library/external/errbit/Errbit.php';

		Errbit::instance()->configure(array(
		   'api_key' => ERRBIT_API_KEY,
		   'host' => 'errors.sumocoders.be',
		   'secure' => true,
		   'port' => 443,
		))->start();

		// overrule the exceptionhandler
		define('SPOON_EXCEPTION_CALLBACK', __CLASS__ . '::exceptionHandler');
	}
}

// create instance
new Sumo();