<?php

namespace Frontend\Modules\FormBuilder\EventListener;

use Common\Mailer\Message;
use Swift_Mailer;
use Common\ModulesSettings;
use Frontend\Core\Language\Language;
use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Swift_Mime_SimpleMessage;
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

    /**
     * @param Swift_Mailer $mailer
     * @param ModulesSettings $modulesSettings
     */
    public function __construct(
        Swift_Mailer $mailer,
        ModulesSettings $modulesSettings
    ) {
        $this->mailer = $mailer;
        $this->modulesSettings = $modulesSettings;
    }

    /**
     * @param FormBuilderSubmittedEvent $event
     */
    public function onFormSubmitted(FormBuilderSubmittedEvent $event)
    {
        $form = $event->getForm();
        $fieldData = $this->getEmailFields($event->getData());

        // need to send mail
        if ($form['method'] == 'database_email') {
            $this->mailer->send($this->getMessage($form, $fieldData));
        }

        // check if we need to send confirmation mails
        foreach ($form['fields'] as $field) {
            if (array_key_exists('send_confirmation_mail_to', $field['settings']) &&
                $field['settings']['send_confirmation_mail_to'] === true
            ) {
                $email = $fieldData[$field['id']]['value'];
                $this->mailer->send($this->getMessage($form, $fieldData, $email, true));
            }
        }
    }

    /**
     * @param array $form
     * @param array $fieldData
     * @param string|null $to
     * @param bool $isConfirmationMail
     *
     * @return Swift_Mime_SimpleMessage
     */
    private function getMessage(array $form, array $fieldData, $to = null, $isConfirmationMail = false)
    {
        $from = $this->modulesSettings->get('Core', 'mailer_from');

        $message = Message::newInstance(sprintf(Language::getMessage('FormBuilderSubject'), $form['name']))
            ->parseHtml(
                '/FormBuilder/Layout/Templates/Mails/Form.html.twig',
                array(
                    'sentOn' => time(),
                    'name' => $form['name'],
                    'fields' => $fieldData,
                    'is_confirmation_mail' => $isConfirmationMail,
                ),
                true
            )
            ->setTo(($to === null) ? $form['email'] : $to)
            ->setFrom(array($from['email'] => $from['name']))
        ;

        // check if we have a replyTo email set
        foreach ($form['fields'] as $field) {
            if (array_key_exists('reply_to', $field['settings']) &&
                $field['settings']['reply_to'] === true
            ) {
                $email = $fieldData[$field['id']]['value'];
                $message->setReplyTo(array($email => $email));
            }
        }
        if ($message->getReplyTo() === null) {
            $replyTo = $this->modulesSettings->get('Core', 'mailer_reply_to');
            $message->setReplyTo(array($replyTo['email'] => $replyTo['name']));
        }

        return $message;
    }

    /**
     * Converts the data to make sure it is nicely usable in the email
     *
     * @param  array $data
     *
     * @return array
     */
    protected function getEmailFields($data)
    {
        return array_map(
            function ($item) {
                $value = unserialize($item['value']);

                return array(
                    'label' => $item['label'],
                    'value' => (is_array($value)
                        ? implode(',', $value)
                        : nl2br($value)
                    ),
                );
            },
            $data
        );
    }
}
