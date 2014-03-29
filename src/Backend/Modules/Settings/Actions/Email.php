<?php

namespace Backend\Modules\Settings\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the email-action, it will display a form to set email settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Email extends BackendBaseActionIndex
{
    /**
     * Is the user a god user?
     *
     * @var bool
     */
    protected $isGod = false;

    /**
     * The form instance
     *
     * @var    BackendForm
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
        $this->isGod = BackendAuthentication::getUser()->isGod();

        $this->frm = new BackendForm('settingsEmail');

        // email settings
        $mailerFrom = BackendModel::getModuleSetting('Core', 'mailer_from');
        $this->frm->addText('mailer_from_name', (isset($mailerFrom['name'])) ? $mailerFrom['name'] : '');
        $this->frm->addText('mailer_from_email', (isset($mailerFrom['email'])) ? $mailerFrom['email'] : '');
        $mailerTo = BackendModel::getModuleSetting('Core', 'mailer_to');
        $this->frm->addText('mailer_to_name', (isset($mailerTo['name'])) ? $mailerTo['name'] : '');
        $this->frm->addText('mailer_to_email', (isset($mailerTo['email'])) ? $mailerTo['email'] : '');
        $mailerReplyTo = BackendModel::getModuleSetting('Core', 'mailer_reply_to');
        $this->frm->addText('mailer_reply_to_name', (isset($mailerReplyTo['name'])) ? $mailerReplyTo['name'] : '');
        $this->frm->addText('mailer_reply_to_email', (isset($mailerReplyTo['email'])) ? $mailerReplyTo['email'] : '');


        if ($this->isGod) {
            $mailerType = BackendModel::getModuleSetting('Core', 'mailer_type', 'mail');
            $this->frm->addDropdown('mailer_type', array('mail' => 'PHP\'s mail', 'smtp' => 'SMTP'), $mailerType);

            // smtp settings
            $this->frm->addText('smtp_server', BackendModel::getModuleSetting('Core', 'smtp_server', ''));
            $this->frm->addText('smtp_port', BackendModel::getModuleSetting('Core', 'smtp_port', 25));
            $this->frm->addText('smtp_username', BackendModel::getModuleSetting('Core', 'smtp_username', ''));
            $this->frm->addPassword('smtp_password', BackendModel::getModuleSetting('Core', 'smtp_password', ''));
            $this->frm->addDropdown(
                'smtp_secure_layer',
                array('no' => ucfirst(BL::lbl('None')), 'ssl' => 'SSL', 'tls' => 'TLS'),
                BackendModel::getModuleSetting('Core', 'smtp_secure_layer', 'no')
            );
        }

        $this->tpl->assign('isGod', $this->isGod);
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
        if ($this->frm->isSubmitted()) {
            // validate required fields
            $this->frm->getField('mailer_from_name')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('mailer_from_email')->isEmail(BL::err('EmailIsInvalid'));
            $this->frm->getField('mailer_to_name')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('mailer_to_email')->isEmail(BL::err('EmailIsInvalid'));
            $this->frm->getField('mailer_reply_to_name')->isFilled(BL::err('FieldIsRequired'));
            $this->frm->getField('mailer_reply_to_email')->isEmail(BL::err('EmailIsInvalid'));

            if ($this->isGod) {
                // SMTP type was chosen
                if ($this->frm->getField('mailer_type')->getValue() == 'smtp') {
                    // server & port are required
                    $this->frm->getField('smtp_server')->isFilled(BL::err('FieldIsRequired'));
                    $this->frm->getField('smtp_port')->isFilled(BL::err('FieldIsRequired'));
                }
            }

            // no errors ?
            if ($this->frm->isCorrect()) {
                // e-mail settings
                BackendModel::setModuleSetting(
                    'Core',
                    'mailer_from',
                    array(
                         'name' => $this->frm->getField('mailer_from_name')->getValue(),
                         'email' => $this->frm->getField('mailer_from_email')->getValue()
                    )
                );
                BackendModel::setModuleSetting(
                    'Core',
                    'mailer_to',
                    array(
                         'name' => $this->frm->getField('mailer_to_name')->getValue(),
                         'email' => $this->frm->getField('mailer_to_email')->getValue()
                    )
                );
                BackendModel::setModuleSetting(
                    'Core',
                    'mailer_reply_to',
                    array(
                         'name' => $this->frm->getField('mailer_reply_to_name')->getValue(),
                         'email' => $this->frm->getField('mailer_reply_to_email')->getValue()
                    )
                );


                if ($this->isGod) {
                    BackendModel::setModuleSetting(
                        'Core',
                        'mailer_type',
                        $this->frm->getField('mailer_type')->getValue()
                    );

                    // smtp settings
                    BackendModel::setModuleSetting(
                        'Core',
                        'smtp_server',
                        $this->frm->getField('smtp_server')->getValue()
                    );
                    BackendModel::setModuleSetting('Core', 'smtp_port', $this->frm->getField('smtp_port')->getValue());
                    BackendModel::setModuleSetting(
                        'Core',
                        'smtp_username',
                        $this->frm->getField('smtp_username')->getValue()
                    );
                    BackendModel::setModuleSetting(
                        'Core',
                        'smtp_password',
                        $this->frm->getField('smtp_password')->getValue()
                    );
                    BackendModel::setModuleSetting(
                        'Core',
                        'smtp_secure_layer',
                        $this->frm->getField('smtp_secure_layer')->getValue()
                    );
                }

                // assign report
                $this->tpl->assign('report', true);
                $this->tpl->assign('reportMessage', BL::msg('Saved'));
                $this->tpl->assign('isGod', $this->isGod);
            }
        }
    }
}
