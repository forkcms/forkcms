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

        // need to send mail
        if ($form['method'] === 'database_email' || $form['method'] === 'email') {
            $this->mailer->send($this->getMessage($form, $fieldData, $form['email_subject']));
        }

        // check if we need to send confirmation mails
        foreach ($form['fields'] as $field) {
            if (array_key_exists('send_confirmation_mail_to', $field['settings']) &&
                $field['settings']['send_confirmation_mail_to'] === true
            ) {
                $email = $fieldData[$field['id']]['value'];
                $this->mailer->send(
                    $this->getMessage($form, $fieldData, $field['settings']['confirmation_mail_subject'], $email, true)
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
        array $form,
        array $fieldData,
        string $subject = null,
        $to = null,
        bool $isConfirmationMail = false
    ) : Swift_Mime_SimpleMessage {
        if ($subject === null) {
            $subject = Language::getMessage('FormBuilderSubject');
        }

        $from = $this->modulesSettings->get('Core', 'mailer_from');

        $message = Message::newInstance(sprintf($subject, $form['name']))
            ->parseHtml(
                '/FormBuilder/Layout/Templates/Mails/' . $form['email_template'],
                [
                    'subject' => $subject,
                    'sentOn' => time(),
                    'name' => $form['name'],
                    'fields' => array_map(
                        function (array $field) : \Swift_Mime_SimpleMessage {
                            $field['value'] = html_entity_decode($field['value']);

                            return $field;
                        },
                        $fieldData
                    ),
                    'is_confirmation_mail' => $isConfirmationMail,
                ],
                true
            )
            ->setTo(($to === null) ? $form['email'] : $to)
            ->setFrom([$from['email'] => $from['name']])
        ;

        // check if we have a replyTo email set
        foreach ($form['fields'] as $field) {
            if (array_key_exists('reply_to', $field['settings']) &&
                $field['settings']['reply_to'] === true
            ) {
                $email = $fieldData[$field['id']]['value'];
                $message->setReplyTo([$email => $email]);
            }
        }
        if ($message->getReplyTo() === null) {
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
