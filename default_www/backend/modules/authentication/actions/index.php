<?php

/**
 * AUthenticationIndex
 *
 * This is the index-action (default), it will display the login screen
 *
 * @package		backend
 * @subpackage	authentication
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class AuthenticationIndex extends BackendBaseActionIndex
{
	/**
	 * Form instance
	 *
	 * @var	SpoonForm
	 */
	private $frm;


	/**
	 * TextFields
	 *
	 * @var	SpoonTextField
	 */
	private $txtUsername, $txtPassword;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the error
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create the form
		$this->frm = new SpoonForm('login', BackendModel::createUrlForAction());

		// create elements
		$this->txtUsername = new SpoonTextField('backend_username', '', 255);
		$this->txtPassword = new SpoonPasswordField('backend_password', '', 255);

		// add elements
		$this->frm->add($this->txtUsername, $this->txtPassword);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// required fields
			$this->txtUsername->isFilled(BL::err('UsernameIsRequired'));
			$this->txtPassword->isFilled(BL::err('PasswordIsRequired'));

			// no errors in the form?
			if($this->frm->getCorrect())
			{
				// login the user, if this can't be done it will return false
				if(BackendAuthentication::loginUser($this->txtUsername->getValue(), $this->txtPassword->getValue()))
				{
					// get the redirect-url from the url
					$redirectUrl = $this->getParameter('querystring');

					// if there isn't a redirect url we will redirect to the dashboard
					if($redirectUrl === null) $redirectUrl = BackendModel::createUrlForAction(null, 'dashboard');

					// redirect to the correct url (url the user was looking for or fallback)
					$this->redirect($redirectUrl);
				}

				// we couldn't log in so PISS OFF
				else $this->tpl->assign('hasError', true);
			}
		}
	}



	/**
	 * Parse the action into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}
}
?>