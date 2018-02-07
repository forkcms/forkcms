<?php

namespace Backend\Modules\Settings\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use App\Component\Locale\BackendLanguage;
use App\Service\Mailer\TransportFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * This test-email-action will test the mail-connection
 */
class TestEmailConnection extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        $fromEmail = $this->getRequest()->request->get('mailer_from_email', '');
        $fromName = $this->getRequest()->request->get('mailer_from_name', '');
        $toEmail = $this->getRequest()->request->get('mailer_to_email', '');
        $toName = $this->getRequest()->request->get('mailer_to_name', '');
        $replyToEmail = $this->getRequest()->request->get('mailer_reply_to_email', '');
        $replyToName = $this->getRequest()->request->get('mailer_reply_to_name', '');

        // init validation
        $errors = [];

        // validate
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['from'] = BackendLanguage::err('EmailIsInvalid');
        }
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['to'] = BackendLanguage::err('EmailIsInvalid');
        }
        if (!filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['reply'] = BackendLanguage::err('EmailIsInvalid');
        }

        // got errors?
        if (!empty($errors)) {
            $this->output(
                Response::HTTP_BAD_REQUEST,
                ['errors' => $errors],
                'invalid fields'
            );

            return;
        }

        $message = new \Swift_Message('Test');
        $message
            ->setFrom([$fromEmail => $fromName])
            ->setTo([$toEmail => $toName])
            ->setReplyTo([$replyToEmail => $replyToName])
            ->setBody(BackendLanguage::msg('TestMessage'), 'text/plain')
        ;

        $mailerType = $this->getRequest()->request->get('mailer_type');
        if (!in_array($mailerType, ['smtp', 'sendmail'])) {
            $mailerType = 'sendmail';
        }
        $transport = TransportFactory::create(
            $mailerType,
            $this->getRequest()->request->get('smtp_server', ''),
            $this->getRequest()->request->getInt('smtp_port', 25),
            $this->getRequest()->request->get('smtp_username', ''),
            $this->getRequest()->request->get('smtp_password', ''),
            $this->getRequest()->request->get('smtp_secure_layer', '')
        );
        $mailer = new \Swift_Mailer($transport);

        try {
            if ($mailer->send($message)) {
                $this->output(Response::HTTP_OK, null, '');

                return;
            }

            $this->output(Response::HTTP_INTERNAL_SERVER_ERROR, null, 'unknown');
        } catch (\Exception $e) {
            $this->output(Response::HTTP_INTERNAL_SERVER_ERROR, null, $e->getMessage());
        }
    }
}
