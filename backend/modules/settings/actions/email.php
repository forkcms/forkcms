<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the email-action, it will display a form to set email settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendSettingsEmail extends BackendBaseActionIndex
{
	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	private $frm;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('settingsEmail');

		// email settings
		$mailerType = BackendModel::getModuleSetting('core', 'mailer_type', 'mail');
		$this->frm->addDropdown('mailer_type', array('mail' => 'PHP\'s mail', 'smtp' => 'SMTP'), $mailerType);
		$mailerFrom = BackendModel::getModuleSetting('core', 'mailer_from');
		$this->frm->addText('mailer_from_name', (isset($mailerFrom['name'])) ? $mailerFrom['name'] : '');
		$this->frm->addText('mailer_from_email', (isset($mailerFrom['email'])) ? $mailerFrom['email'] : '');
		$mailerTo = BackendModel::getModuleSetting('core', 'mailer_to');
		$this->frm->addText('mailer_to_name', (isset($mailerTo['name'])) ? $mailerTo['name'] : '');
		$this->frm->addText('mailer_to_email', (isset($mailerTo['email'])) ? $mailerTo['email'] : '');
		$mailerReplyTo = BackendModel::getModuleSetting('core', 'mailer_reply_to');
		$this->frm->addText('mailer_reply_to_name', (isset($mailerReplyTo['name'])) ? $mailerReplyTo['name'] : '');
		$this->frm->addText('mailer_reply_to_email', (isset($mailerReplyTo['email'])) ? $mailerReplyTo['email'] : '');

		// smtp settings
		$this->frm->addText('smtp_server', BackendModel::getModuleSetting('core', 'smtp_server', ''));
		$this->frm->addText('smtp_port', BackendModel::getModuleSetting('core', 'smtp_port', 25));
		$this->frm->addText('smtp_username', BackendModel::getModuleSetting('core', 'smtp_username', ''));
		$this->frm->addPassword('smtp_password', BackendModel::getModuleSetting('core', 'smtp_password', ''));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// parse the form
		$this->frm->parse($this->tpl);
	}

	/**
	 * Validates the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// validate required fields
			$this->frm->getField('mailer_from_name')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('mailer_from_email')->isEmail(BL::err('EmailIsInvalid'));
			$this->frm->getField('mailer_to_name')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('mailer_to_email')->isEmail(BL::err('EmailIsInvalid'));
			$this->frm->getField('mailer_reply_to_name')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('mailer_reply_to_email')->isEmail(BL::err('EmailIsInvalid'));

			// SMTP type was chosen
			if($this->frm->getField('mailer_type')->getValue() == 'smtp')
			{
				// server & port are required
				$this->frm->getField('smtp_server')->isFilled(BL::err('FieldIsRequired'));
				$this->frm->getField('smtp_port')->isFilled(BL::err('FieldIsRequired'));
			}

			// no errors ?
			if($this->frm->isCorrect())
			{
				// e-mail settings
				BackendModel::setModuleSetting('core', 'mailer_type', $this->frm->getField('mailer_type')->getValue());
				BackendModel::setModuleSetting('core', 'mailer_from', array('name' => $this->frm->getField('mailer_from_name')->getValue(), 'email' => $this->frm->getField('mailer_from_email')->getValue()));
				BackendModel::setModuleSetting('core', 'mailer_to', array('name' => $this->frm->getField('mailer_to_name')->getValue(), 'email' => $this->frm->getField('mailer_to_email')->getValue()));
				BackendModel::setModuleSetting('core', 'mailer_reply_to', array('name' => $this->frm->getField('mailer_reply_to_name')->getValue(), 'email' => $this->frm->getField('mailer_reply_to_email')->getValue()));

				// smtp settings
				BackendModel::setModuleSetting('core', 'smtp_server', $this->frm->getField('smtp_server')->getValue());
				BackendModel::setModuleSetting('core', 'smtp_port', $this->frm->getField('smtp_port')->getValue());
				BackendModel::setModuleSetting('core', 'smtp_username', $this->frm->getField('smtp_username')->getValue());
				BackendModel::setModuleSetting('core', 'smtp_password', $this->frm->getField('smtp_password')->getValue());

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}
