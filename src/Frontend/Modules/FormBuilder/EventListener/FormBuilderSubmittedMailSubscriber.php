<?php

namespace Frontend\Modules\FormBuilder\EventListener;

use Swift_Mailer;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;

class FormBuilderSubmittedMailSubscriber
{
    protected $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onFormSubmitted(FormBuilderSubmittedEvent $event)
    {
        $form = $event->getForm();

        // need to send mail
        if ($form['method'] == 'database_email') {
            // build our message
            $from = FrontendModel::getModuleSetting('Core', 'mailer_from');
            $message = \Common\Mailer\Message::newInstance(sprintf(
                    FL::getMessage('FormBuilderSubject'),
                    $form['name']
                ))
                ->parseHtml(
                    FRONTEND_MODULES_PATH . '/FormBuilder/Layout/Templates/Mails/Form.tpl',
                    array(
                        'sentOn' => time(),
                        'name' => $form['name'],
                        'fields' => $this->getEmailFields($event->getData()),
                    ),
                    true
                )
                ->setTo($form['email'])
                ->setFrom(array($from['email'] => $from['name']))
            ;

            // check if we have a replyTo email set
            foreach ($form['fields'] as $field) {
                if (array_key_exists('reply_to', $field['settings']) &&
                    $field['settings']['reply_to'] === true
                ) {
                    $email = $this->frm->getField('field' . $field['id'])->getValue();
                    $message->setReplyTo(array($email => $email));
                }
            }
            if ($message->getReplyTo() === null) {
                $replyTo = FrontendModel::getModuleSetting('Core', 'mailer_reply_to');
                $message->setReplyTo(array($replyTo['email'] => $replyTo['name']));
            }

            $this->mailer->send($message);
        }
    }

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
                    )
                );
            },
            $data
        );
    }
}
