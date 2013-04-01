<?php

/**
 * Our Sumo class with specific Sumo stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Sumo
{
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
	public function initErrbit($errbitApiKey)
	{
		Errbit::instance()->configure(array(
		   'api_key' => $errbitApiKey,
		   'host' => 'errors.sumocoders.be',
		   'secure' => true,
		   'port' => 443,
		))->start();

		// overrule the exceptionhandler
		define('SPOON_EXCEPTION_CALLBACK', __CLASS__ . '::exceptionHandler');
	}
}