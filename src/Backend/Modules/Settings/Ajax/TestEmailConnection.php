<?php

namespace Backend\Modules\Settings\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Common\Mailer\TransportFactory;
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
        if ($fromEmail == '' || !\SpoonFilter::isEmail($fromEmail)) {
            $errors['from'] = BL::err('EmailIsInvalid');
        }
        if ($toEmail == '' || !\SpoonFilter::isEmail($toEmail)) {
            $errors['to'] = BL::err('EmailIsInvalid');
        }
        if ($replyToEmail == '' || !\SpoonFilter::isEmail($replyToEmail)) {
            $errors['reply'] = BL::err('EmailIsInvalid');
        }

        // got errors?
        if (!empty($errors)) {
            $this->output(
                self::BAD_REQUEST,
                ['errors' => $errors],
                'invalid fields'
            );
        } else {
            $message = \Swift_Message::newInstance('Test')
                ->setFrom([$fromEmail => $fromName])
                ->setTo([$toEmail => $toName])
                ->setReplyTo([$replyToEmail => $replyToName])
                ->setBody(BL::msg('TestMessage'), 'text/plain')
            ;

            $mailerType = $this->getRequest()->request->get('mailer_type');
            if (!in_array($mailerType, ['smtp', 'mail'])) {
                $mailerType = 'mail';
            }
            $transport = TransportFactory::create(
                $mailerType,
                $this->getRequest()->request->get('smtp_server', ''),
                $this->getRequest()->request->getInt('smtp_port', 25),
                $this->getRequest()->request->get('smtp_username', ''),
                $this->getRequest()->request->get('smtp_password', ''),
                $this->getRequest()->request->get('smtp_secure_layer', '')
            );
            $mailer = \Swift_Mailer::newInstance($transport);

            try {
                if ($mailer->send($message)) {
                    $this->output(self::OK, null, '');
                } else {
                    $this->output(self::ERROR, null, 'unknown');
                }
            } catch (\Exception $e) {
                $this->output(self::ERROR, null, $e->getMessage());
            }
        }
    }
}
