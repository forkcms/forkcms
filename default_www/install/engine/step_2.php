<?php

class InstallerStep2 extends InstallerStep
{
	/**
	 * Executes this step.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load form
		$this->loadForm();

		// validate form
		$this->validateForm();

		// parse form
		$this->parseForm();

		// show output
		$this->tpl->display('layout/templates/2.tpl');
	}


	/**
	 * Is this step allowed.
	 *
	 * @return	bool
	 */
	public static function isAllowed()
	{
		return InstallerStep1::checkRequirements();
	}


	/**
	 * Loads the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		$this->frm->addText('hostname', SpoonSession::exists('db_hostname') ? SpoonSession::get('db_hostname') : null);
		$this->frm->addText('database', SpoonSession::exists('db_database') ? SpoonSession::get('db_database') : null);
		$this->frm->addText('username', SpoonSession::exists('db_username') ? SpoonSession::get('db_username') : null);
		$this->frm->addPassword('password', SpoonSession::exists('db_password') ? SpoonSession::get('db_password') : null);
	}


	/**
	 * Validate the form based on the variables in $_POST
	 *
	 * @return	void
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
				/*
				 * Test the database connection details.
				 */
				try
				{
					// create instance
					$db = new SpoonDatabase('mysql', $this->frm->getField('hostname')->getValue(), $this->frm->getField('username')->getValue(), $this->frm->getField('password')->getValue(), $this->frm->getField('database')->getValue());

					// test table
					$table = 'test'. uniqid();

					// attempt to create table
					$db->execute('DROP TABLE IF EXISTS '. $table .';');
					$db->execute('CREATE TABLE IF NOT EXISTS '. $table .' (id int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;');

					// drop table
					$db->drop($table);
				}

				/*
				 * Catch possible exceptions
				 */
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

					// redirect
					SpoonHTTP::redirect('index.php?step=3');
				}
			}
		}
	}
}

?>