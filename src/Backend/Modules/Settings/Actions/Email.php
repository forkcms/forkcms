<?php

namespace Backend\Modules\Settings\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use App\Component\Locale\BackendLanguage;

/**
 * This is the email-action, it will display a form to set email settings
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
     * @var BackendForm
     */
    private $form;

    public function execute(): void
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->isGod = BackendAuthentication::getUser()->isGod();

        $this->form = new BackendForm('settingsEmail');

        // email settings
        $mailerFrom = $this->get('forkcms.settings')->get('Core', 'mailer_from');
        $this->form->addText('mailer_from_name', $mailerFrom['name'] ?? '');
        $this->form
            ->addText('mailer_from_email', $mailerFrom['email'] ?? '')
            ->setAttribute('type', 'email')
        ;
        $mailerTo = $this->get('forkcms.settings')->get('Core', 'mailer_to');
        $this->form->addText('mailer_to_name', $mailerTo['name'] ?? '');
        $this->form
            ->addText('mailer_to_email', $mailerTo['email'] ?? '')
            ->setAttribute('type', 'email')
        ;
        $mailerReplyTo = $this->get('forkcms.settings')->get('Core', 'mailer_reply_to');
        $this->form->addText('mailer_reply_to_name', $mailerReplyTo['name'] ?? '');
        $this->form
            ->addText('mailer_reply_to_email', $mailerReplyTo['email'] ?? '')
            ->setAttribute('type', 'email')
        ;

        if ($this->isGod) {
            $mailerType = $this->get('forkcms.settings')->get('Core', 'mailer_type', 'sendmail');
            $this->form->addDropdown('mailer_type', ['sendmail' => 'sendmail', 'smtp' => 'SMTP'], $mailerType);

            // smtp settings
            $this->form->addText('smtp_server', $this->get('forkcms.settings')->get('Core', 'smtp_server', ''));
            $this->form->addText('smtp_port', $this->get('forkcms.settings')->get('Core', 'smtp_port', 25));
            $this->form->addText('smtp_username', $this->get('forkcms.settings')->get('Core', 'smtp_username', ''));
            $this->form->addPassword('smtp_password', $this->get('forkcms.settings')->get('Core', 'smtp_password', ''));
            $this->form->addDropdown(
                'smtp_secure_layer',
                ['no' => ucfirst(BackendLanguage::lbl('None')), 'ssl' => 'SSL', 'tls' => 'TLS'],
                $this->get('forkcms.settings')->get('Core', 'smtp_secure_layer', 'no')
            );
        }

        $this->template->assign('isGod', $this->isGod);
    }

    protected function parse(): void
    {
        parent::parse();

        // parse the form
        $this->form->parse($this->template);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // validate required fields
            $this->form->getField('mailer_from_name')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('mailer_from_email')->isEmail(BackendLanguage::err('EmailIsInvalid'));
            $this->form->getField('mailer_to_name')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('mailer_to_email')->isEmail(BackendLanguage::err('EmailIsInvalid'));
            $this->form->getField('mailer_reply_to_name')->isFilled(BackendLanguage::err('FieldIsRequired'));
            $this->form->getField('mailer_reply_to_email')->isEmail(BackendLanguage::err('EmailIsInvalid'));

            if ($this->isGod) {
                // SMTP type was chosen
                if ($this->form->getField('mailer_type')->getValue() == 'smtp') {
                    // server & port are required
                    $this->form->getField('smtp_server')->isFilled(BackendLanguage::err('FieldIsRequired'));
                    $this->form->getField('smtp_port')->isFilled(BackendLanguage::err('FieldIsRequired'));
                }
            }

            // no errors ?
            if ($this->form->isCorrect()) {
                // e-mail settings
                $this->get('forkcms.settings')->set(
                    'Core',
                    'mailer_from',
                    [
                        'name' => $this->form->getField('mailer_from_name')->getValue(),
                        'email' => $this->form->getField('mailer_from_email')->getValue(),
                    ]
                );
                $this->get('forkcms.settings')->set(
                    'Core',
                    'mailer_to',
                    [
                        'name' => $this->form->getField('mailer_to_name')->getValue(),
                        'email' => $this->form->getField('mailer_to_email')->getValue(),
                    ]
                );
                $this->get('forkcms.settings')->set(
                    'Core',
                    'mailer_reply_to',
                    [
                        'name' => $this->form->getField('mailer_reply_to_name')->getValue(),
                        'email' => $this->form->getField('mailer_reply_to_email')->getValue(),
                    ]
                );

                if ($this->isGod) {
                    $this->get('forkcms.settings')->set(
                        'Core',
                        'mailer_type',
                        $this->form->getField('mailer_type')->getValue()
                    );

                    // smtp settings
                    $this->get('forkcms.settings')->set(
                        'Core',
                        'smtp_server',
                        $this->form->getField('smtp_server')->getValue()
                    );
                    $this->get('forkcms.settings')->set('Core', 'smtp_port', $this->form->getField('smtp_port')->getValue());
                    $this->get('forkcms.settings')->set(
                        'Core',
                        'smtp_username',
                        $this->form->getField('smtp_username')->getValue()
                    );
                    $this->get('forkcms.settings')->set(
                        'Core',
                        'smtp_password',
                        $this->form->getField('smtp_password')->getValue()
                    );
                    $this->get('forkcms.settings')->set(
                        'Core',
                        'smtp_secure_layer',
                        $this->form->getField('smtp_secure_layer')->getValue()
                    );
                }

                // assign report
                $this->template->assign('report', true);
                $this->template->assign('reportMessage', BackendLanguage::msg('Saved'));
            }
        }
    }
}
