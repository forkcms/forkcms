<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	contact
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendContactIndex extends FrontendBaseBlock
{
	/**
	 * FrontendForm instance
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load
		$this->loadForm();

		// validate
		$this->validateForm();

		// parse
		$this->parse();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// get values from cookie
		$author = (SpoonCookie::exists('comment_author')) ? SpoonCookie::get('comment_author') : null;
		$email = (SpoonCookie::exists('comment_email')) ? SpoonCookie::get('comment_email') : null;

		// create the form
		$this->frm = new FrontendForm('contact', null, null, 'contactForm');

		// create & add elements
		$this->frm->addText('author', $author);
		$this->frm->addText('email', $email);
		$this->frm->addTextarea('message');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// form was sent?
		if($this->URL->getParameter('sent') == 'true')
		{
			// show message
			$this->tpl->assign('contactIsSuccess', true);

			// hide form
			$this->tpl->assign('contactHideForm', true);
		}

		// parse the form
		$this->frm->parse($this->tpl);
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
			// validate required fields
			$this->frm->getField('author')->isFilled(FL::err('NameIsRequired'));
			$this->frm->getField('email')->isEmail(FL::err('EmailIsInvalid'));
			$this->frm->getField('message')->isFilled(FL::err('MessageIsRequired'));

			// no errors
			if($this->frm->isCorrect())
			{
				// get values
				$author = $this->frm->getField('author')->getValue();
				$email = $this->frm->getField('email')->getValue();
				$message = $this->frm->getField('message')->getValue();

				// format message
				$message = FrontendTemplateModifiers::cleanupPlainText($message);

				// build variables
				$item['author'] = $author;
				$item['email'] = $email;
				$item['message'] = $message;

				// store author-data in cookies
				try
				{
					// set cookies
					SpoonCookie::set('comment_author', $author, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
					SpoonCookie::set('comment_email', $email, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
				}
				catch(Exception $e)
				{
					// settings cookies isn't allowed, because this isn't a real problem we ignore the exception
				}

				try
				{
					// add email
					FrontendMailer::addEmail(FL::msg('ContactSubject') . ': ' . $author, FRONTEND_MODULES_PATH . '/contact/layout/templates/mails/contact.tpl', $item, null, null, null, null, $email, $author);

					// redirect
					$this->redirect(FrontendNavigation::getURLForBlock('contact') . '?sent=true');
				}
				catch(Exception $e)
				{
					// when debugging we need to see the exceptions
					if(SPOON_DEBUG) throw $e;

					// show error
					$this->tpl->assign('contactHasError', true);
				}
			}

			// show errors
			else $this->tpl->assign('contactHasFormError', true);
		}
	}

}

?>