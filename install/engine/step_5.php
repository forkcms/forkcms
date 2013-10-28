<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 5 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class InstallerStep5 extends InstallerStep
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
		return InstallerStep4::isAllowed() && isset($_SESSION['modules']) && isset($_SESSION['example_data']) && isset($_SESSION['debug_mode']);
	}

	/**
	 * Loads the form.
	 */
	private function loadForm()
	{
		// guess db & username
		$host = $_SERVER['HTTP_HOST'];
		$chunks = explode('.', $host);

		// seems like windows can't handle localhost...
		$dbHost = (substr(PHP_OS, 0, 3) == 'WIN') ? '127.0.0.1' : 'localhost';

		// remove tld
		array_pop($chunks);

		// create base
		$base = implode('_', $chunks);

		// create input fields
		$this->frm->addText('hostname', SpoonSession::exists('db_hostname') ? SpoonSession::get('db_hostname') : $dbHost);
		$this->frm->addText('port', SpoonSession::exists('db_port') ? SpoonSession::get('db_port') : 3306, 10);
		$this->frm->addText('database', SpoonSession::exists('db_database') ? SpoonSession::get('db_database') : $base);
		$this->frm->addText('username', SpoonSession::exists('db_username') ? SpoonSession::get('db_username') : $base);
		$this->frm->addPassword('password', SpoonSession::exists('db_password') ? SpoonSession::get('db_password') : null);
	}

	/**
	 * Validate the form based on the variables in $_POST
	 */
	private function validateForm()
	{
		// form submitted
		if($this->frm->isSubmitted())
		{
			// database settings
			$this->frm->getField('hostname')->isFilled('This field is required.');
			$this->frm->getField('database')->isFilled('This field is required.');
			$this->frm->getField('username')->isFilled('This field is required.');
			$this->frm->getField('password')->isFilled('This field is required.');

			// all filled out
			if($this->frm->getField('hostname')->isFilled() && $this->frm->getField('database')->isFilled() && $this->frm->getField('username')->isFilled() && $this->frm->getField('password')->isFilled())
			{
				// test the database connection details
				try
				{
					// get port
					$port = ($this->frm->getField('port')->isFilled()) ? $this->frm->getField('port')->getValue() : 3306;

					// create instance
					$db = new SpoonDatabase('mysql', $this->frm->getField('hostname')->getValue(), $this->frm->getField('username')->getValue(), $this->frm->getField('password')->getValue(), $this->frm->getField('database')->getValue(), $port);

					// test table
					$table = 'test' . time();

					// attempt to create table
					$db->execute('DROP TABLE IF EXISTS ' . $table);
					$db->execute('CREATE TABLE ' . $table . ' (id int(11) NOT NULL) ENGINE=MyISAM');

					// drop table
					$db->drop($table);
				}

				// catch possible exceptions
				catch(Exception $e)
				{
					// add errors
					$this->frm->addError('Problem with database credentials');

					// show error
					$this->tpl->assign('formError', $e->getMessage());
				}

				// all valid
				if($this->frm->isCorrect())
				{
					// update session
					SpoonSession::set('db_hostname', $this->frm->getField('hostname')->getValue());
					SpoonSession::set('db_database', $this->frm->getField('database')->getValue());
					SpoonSession::set('db_username', $this->frm->getField('username')->getValue());
					SpoonSession::set('db_password', $this->frm->getField('password')->getValue());
					SpoonSession::set('db_port', $this->frm->getField('port')->getValue());

					// redirect
					SpoonHTTP::redirect('index.php?step=6');
				}
			}
		}
	}
}
