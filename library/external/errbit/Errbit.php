<?php

/**
 * Errbit PHP Notifier.
 *
 * Copyright © Flippa.com Pty. Ltd.
 * See the LICENSE file for details.
 */

require_once dirname(__FILE__) . '/Errbit/Exception.php';

require_once dirname(__FILE__) . '/Errbit/XmlBuilder.php';
require_once dirname(__FILE__) . '/Errbit/Notice.php';

require_once dirname(__FILE__) . '/Errbit/Errors/Base.php';
require_once dirname(__FILE__) . '/Errbit/Errors/Notice.php';
require_once dirname(__FILE__) . '/Errbit/Errors/Warning.php';
require_once dirname(__FILE__) . '/Errbit/Errors/Error.php';
require_once dirname(__FILE__) . '/Errbit/Errors/Fatal.php';

require_once dirname(__FILE__) . '/Errbit/ErrorHandlers.php';

/**
 * The Errbit client.
 *
 * @example Configuring the client
 *    Errbit::instance()->configure(array( ... ))->start();
 *
 * @example Notify an Exception manually
 *    Errbit::instance()->notify($exception);
 */
class Errbit {
	private static $_instance = null;

	/**
	 * Get a singleton instance of the client.
	 *
	 * This is the intended way to access the Errbit client.
	 *
	 * @return [Errbit]
	 *   a singleton
	 */
	public static function instance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	const VERSION       = '0.0.1';
	const API_VERSION   = '2.2';
	const PROJECT_NAME  = 'errbit-php';
	const PROJECT_URL   = 'https://github.com/flippa/errbit-php';
	const NOTICES_PATH  = '/notifier_api/v2/notices/';

	private $_config;
	private $_observers = array();

	/**
	 * Initialize a new client with the given config.
	 *
	 * This is made public for flexibility, though it is not expected you
	 * should use it.
	 *
	 * @param [Array] $config
	 *   the configuration for the API
	 */
	public function __construct($config = array()) {
		$this->_config = $config;
	}

	/**
	 * Add a handler to be invoked after a notification occurs.
	 *
	 * @param [Callback] $callback
	 *   any callable function
	 *
	 * @return [Errbit]
	 *   the current instance
	 */
	public function onNotify($callback) {
		if (!is_callable($callback)) {
			throw new Errbit_Exception('Notify callback must be callable');
		}

		$this->_observers[] = $callback;

		return $this;
	}

	/**
	 * Set the full configuration for the client.
	 *
	 * The only required keys are `api_key' and `host', but other supported
	 * options are:
	 *
	 *   - api_key
	 *   - host
	 *   - port
	 *   - secure
	 *   - project_root
	 *   - environment_name
	 *   - url
	 *   - controller
	 *   - action
	 *   - session_data
	 *   - parameters
	 *   - cgi_data
	 *   - params_filters
	 *   - backtrace_filters
	 *
	 * @param [Array] $config
	 *   the full configuration
	 *
	 * @return [Errbit]
	 *   the current instance of the client
	 */
	public function configure($config = array()) {
		$this->_config = array_merge($this->_config, $config);
		$this->_checkConfig();
		return $this;
	}

	/**
	 * Register all error handlers around this instance.
	 *
	 * @param [Array] $handlers
	 *   an array of handler names (one or all of 'exception', 'error', 'fatal')
	 *
	 * @return [Errbit]
	 *   the current instance
	 */
	public function start($handlers = array('exception', 'error', 'fatal')) {
		$this->_checkConfig();
		Errbit_ErrorHandlers::register($this, $handlers);
		return $this;
	}

	/**
	 * Notify an individual exception manually.
	 *
	 * @param [Exception] $exception
	 *   the Exception to notify (errors must first be converted)
	 *
	 * @param [Array] $options
	 *   an array of options, which override the client configuration
	 *
	 * @return [Errbit]
	 *   the current instance
	 */
	public function notify($exception, $options = array()) {
		$config = array_merge($this->_config, $options);

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL            => $this->_buildApiUrl(),
			CURLOPT_HEADER         => true,
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => $this->_buildNoticeFor($exception, $config),
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: text/xml',
				'Accept: text/xml, application/xml'
			)
		));
		curl_exec($ch);

		foreach ($this->_observers as $observer) {
			$observer($exception, $config);
		}

		return $this;
	}

	// -- Private Methods

	private function _checkConfig() {
		if (empty($this->_config['api_key'])) {
			throw new Errbit_Exception("`api_key' must be configured");
		}

		if (empty($this->_config['host'])) {
			throw new Errbit_Exception("`host' must be configured");
		}

		if (empty($this->_config['port'])) {
			$this->_config['port'] = !empty($this->_config['secure']) ? 443 : 80;
		}

		if (!isset($this->_config['secure'])) {
			$this->_config['secure'] = ($this->_config['port'] == 443);
		}

		if (empty($this->_config['hostname'])) {
			$this->_config['hostname'] = gethostname() ? gethostname() : '<unknown>';
		}

		if (empty($this->_config['project_root'])) {
			$this->_config['project_root'] = dirname(__FILE__);
		}

		if (empty($this->_config['environment_name'])) {
			$this->_config['environment_name'] = 'development';
		}

		if (!isset($this->_config['params_filters'])) {
			$this->_config['params_filters'] = array('/password/');
		}

		if (!isset($this->_config['backtrace_filters'])) {
			$this->_config['backtrace_filters'] = array(
				sprintf('/^%s/', preg_quote($this->_config['project_root'], '/')) => '[PROJECT_ROOT]'
			);
		}
	}

	private function _buildApiUrl() {
		$this->_checkConfig();
		return implode(
			'',
			array(
				$this->_config['secure'] ? 'https://' : 'http://',
				$this->_config['host'],
				':' . $this->_config['port'],
				self::NOTICES_PATH
			)
		);
	}

	private function _buildNoticeFor($exception, $options) {
		return Errbit_Notice::forException($exception, $options)->asXml();
	}
}
