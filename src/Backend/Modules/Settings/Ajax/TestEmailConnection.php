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

/**
 * This test-email-action will test the mail-connection
 */
class TestEmailConnection extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $fromEmail = \SpoonFilter::getPostValue('mailer_from_email', null, '');
        $fromName = \SpoonFilter::getPostValue('mailer_from_name', null, '');
        $toEmail = \SpoonFilter::getPostValue('mailer_to_email', null, '');
        $toName = \SpoonFilter::getPostValue('mailer_to_name', null, '');
        $replyToEmail = \SpoonFilter::getPostValue('mailer_reply_to_email', null, '');
        $replyToName = \SpoonFilter::getPostValue('mailer_reply_to_name', null, '');

        // init validation
        $errors = array();

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
                array('errors' => $errors),
                'invalid fields'
            );
        } else {
            $message = \Swift_Message::newInstance('Test')
                ->setFrom(array($fromEmail => $fromName))
                ->setTo(array($toEmail => $toName))
                ->setReplyTo(array($replyToEmail => $replyToName))
                ->setBody(BL::msg('TestMessage'), 'text/plain')
            ;

            $transport = TransportFactory::create(
                \SpoonFilter::getPostValue('mailer_type', array('smtp', 'mail'), 'mail'),
                \SpoonFilter::getPostValue('smtp_server', null, ''),
                \SpoonFilter::getPostValue('smtp_port', null, ''),
                \SpoonFilter::getPostValue('smtp_username', null, ''),
                \SpoonFilter::getPostValue('smtp_password', null, ''),
                \SpoonFilter::getPostValue('smtp_secure_layer', null, '')
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
