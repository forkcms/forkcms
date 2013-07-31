<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Step 6 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class InstallerStep6 extends InstallerStep
{
	/**
	 * Executes this step.
	 */
	public function execute()
	{
		$this->loadForm();
		$this->validateForm();
		$this->parseForm();
	}

	/**
	 * Is this step allowed.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return InstallerStep5::isAllowed() && isset($_SESSION['db_hostname']) && isset($_SESSION['db_database']) && isset($_SESSION['db_username']) && isset($_SESSION['db_password']);
	}

	/**
	 * Loads the form.
	 */
	private function loadForm()
	{
		// guess email
		$host = $_SERVER['HTTP_HOST'];

		$this->frm->addText('email', (SpoonSession::exists('email') ? SpoonSession::get('email') : 'info@' . $host));
		$this->frm->addPassword('password', (SpoonSession::exists('password') ? SpoonSession::get('password') : null), null, 'inputPassword', 'inputPasswordError', true);
		$this->frm->addPassword('confirm', (SpoonSession::exists('confirm') ? SpoonSession::get('confirm') : null), null, 'inputPassword', 'inputPasswordError', true);

		// disable autocomplete
		$this->frm->getField('password')->setAttributes(array('autocomplete' => 'off'));
		$this->frm->getField('confirm')->setAttributes(array('autocomplete' => 'off'));
	}

	/**
	 * Validate the form based on the variables in $_POST
	 */
	private function validateForm()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// required fields
			$this->frm->getField('email')->isEmail('Please provide a valid e-mail address.');
			$this->frm->getField('password')->isFilled('This field is required.');
			$this->frm->getField('confirm')->isFilled('This field is required.');
			if($this->frm->getField('password')->getValue() != $this->frm->getField('confirm')->getValue()) $this->frm->getField('confirm')->addError('The passwords do not match.');

			// all valid
			if($this->frm->isCorrect())
			{
				// update session
				SpoonSession::set('email', $this->frm->getField('email')->getValue());
				SpoonSession::set('password', $this->frm->getField('password')->getValue());
				SpoonSession::set('confirm', $this->frm->getField('confirm')->getValue());

				$this->createYAMLConfig();

				// redirect
				SpoonHTTP::redirect('index.php?step=7');
			}
		}
	}

	/**
	 * Writes a config file to app/config/parameters.yml.
	 */
	private function createYAMLConfig()
	{
		// these variables should be parsed inside the config file(s).
		$variables = $this->getConfigurationVariables();

		// map the config templates to their destination filename
		$yamlFiles = array(
			PATH_WWW . '/app/config/parameters.yml.dist' => PATH_WWW . '/app/config/parameters.yml',
		);

		foreach($yamlFiles as $sourceFilename => $destinationFilename)
		{
			$yamlContent = file_get_contents($sourceFilename);
			$yamlContent = str_replace(
				array_keys($variables),
				array_values($variables),
				$yamlContent
			);

			// write app/config/parameters.yml
			$fs = new Filesystem();
			$fs->dumpFile($destinationFilename, $yamlContent);
		}
	}

	/**
	 * @return array A list of variables that should be parsed into the configuration file(s).
	 */
	protected function getConfigurationVariables()
	{
		return array(
			'<debug-mode>'				=> SpoonSession::get('debug_mode') ? 'true' : 'false',
			'<debug-email>'				=> SpoonSession::get('different_debug_email') ? SpoonSession::get('debug_email') : SpoonSession::get('email'),
			'<database-name>'			=> SpoonSession::get('db_database'),
			'<database-host>'			=> addslashes(SpoonSession::get('db_hostname')),
			'<database-user>'			=> addslashes(SpoonSession::get('db_username')),
			'<database-password>'		=> addslashes(SpoonSession::get('db_password')),
			'<database-port>'			=> (SpoonSession::exists('db_port') && SpoonSession::get('db_port') != '') ? addslashes(SpoonSession::get('db_port')) : 3306,
			'<site-protocol>'			=> isset($_SERVER['SERVER_PROTOCOL']) ? (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https') : 'http',
			'<site-domain>'				=> (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'fork.local',
			'<site-default-title>'		=> 'Fork CMS',
			'<site-multilanguage>'		=> SpoonSession::get('multiple_languages') ? 'true' : 'false',
			'<site-default-language>'	=> SpoonSession::get('default_language'),
			'<path-www>'				=> PATH_WWW,
			'<path-library>'			=> PATH_LIBRARY,
			'<action-group-tag>'		=> '\@actiongroup',
			'<action-rights-level>'		=> 7
		);
	}
}
