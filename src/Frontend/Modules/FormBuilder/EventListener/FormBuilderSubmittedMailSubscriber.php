<?php

namespace Frontend\Modules\FormBuilder\EventListener;

use Common\Mailer\Message;
use Swift_Mailer;
use Common\ModulesSettings;
use Frontend\Core\Language\Language;
use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Swift_Mime_SimpleMessage;

/**
 * A Formbuilder submitted event subscriber that will send an email if needed
 */
final class FormBuilderSubmittedMailSubscriber
{
    /**
     * @var ModulesSettings
     */
    protected $modulesSettings;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    public function __construct(
        Swift_Mailer $mailer,
        ModulesSettings $modulesSettings
    ) {
        $this->mailer = $mailer;
        $this->modulesSettings = $modulesSettings;
    }

    public function onFormSubmitted(FormBuilderSubmittedEvent $event): void
    {
        $form = $event->getForm();
        $fieldData = $this->getEmailFields($event->getData());
        $formEmails = $form['emails'];

        // need to send mail
        if (!empty($formEmails)) {
            foreach ($formEmails as $email) {
                $this->mailer->send(
                    $this->getMessage(
                        $email,
                        $fieldData
                    )
                );
            }
        }
    }

    /**
     * @param array $form
     * @param array $fieldData
     * @param string $subject
     * @param string|array|null $to
     * @param bool $isConfirmationMail
     *
     * @return Swift_Mime_SimpleMessage
     */
    private function getMessage(
        array $email,
        array $fieldData
    ) : Swift_Mime_SimpleMessage {
        // send to
        if ($email['email_recipient'] == 'field') {
            $to = $fieldData[$email['email_to_field']]['value'];
        }
        if ($email['email_recipient'] == 'email') {
            $to = $email['email_to_addresses'];
        }

        $message = Message::newInstance($email['email_subject'])
            ->parseHtml(
                '/FormBuilder/Layout/Templates/Mails/' . $email['email_template'],
                [
                    'subject' => $email['email_subject'],
                    'sentOn' => time(),
                    'message' => $email['email_body'],
                    'fields' => array_map(
                        function (array $field) : array {
                            $field['value'] = html_entity_decode($field['value']);

                            return $field;
                        },
                        $fieldData
                    ),
                    'email_data' => $email['email_data'],
                ],
                true
            )
            ->setTo($to)
            ->setFrom([$email['email_from']['email'] => $email['email_from']['name']])
        ;

        // check if we have a replyTo email set
        if (empty($message->getReplyTo())) {
            $replyTo = $this->modulesSettings->get('Core', 'mailer_reply_to');
            $message->setReplyTo([$replyTo['email'] => $replyTo['name']]);
        }

        return $message;
    }

    /**
     * Converts the data to make sure it is nicely usable in the email
     *
     * @param array $data
     *
     * @return array
     */
    protected function getEmailFields(array $data): array
    {
        return array_map(
            function ($item) : array {
                $value = unserialize($item['value']);

                return [
                    'label' => $item['label'],
                    'value' => (is_array($value)
                        ? implode(',', $value)
                        : nl2br($value)
                    ),
                ];
            },
            $data
        );
    }
}
