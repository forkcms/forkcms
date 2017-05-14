<?php

namespace Frontend\Modules\Mailmotor\EventListener;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Common\ModulesSettings;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;

/**
 * Subscribe from formbuilder submitted form
 */
final class SubscribeFromFormBuilderSubmittedForm
{
    /**
     * @var ModulesSettings
     */
    protected $modulesSettings;

    /**
     * @var Subscriber
     */
    protected $subscriber;

    public function __construct(
        Subscriber $subscriber,
        ModulesSettings $modulesSettings
    ) {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
    }

    public function onFormBuilderSubmittedEvent(
        FormBuilderSubmittedEvent $event
    ): void {
        if ($this->modulesSettings->get('Mailmotor', 'automatically_subscribe_from_form_builder_submitted_form', false)) {
            $form = $event->getForm();
            $data = $event->getData();
            $email = null;

            // Check if we have a replyTo email set
            foreach ($form['fields'] as $field) {
                if (array_key_exists('reply_to', $field['settings']) &&
                    $field['settings']['reply_to'] === true
                ) {
                    $email = unserialize($data[$field['id']]['value']);
                }
            }

            // Define language
            $language = array_key_exists('language', $form)
                ? $form['language'] : $this->modulesSettings->get('Core', 'default_language', 'en');

            // We subscribe the replyTo email
            try {
                // Does email exists or not in our mailing list
                $exists = (bool) $this->subscriber->exists($email);

                // We only need to subscribe when not exists
                if (!$exists) {
                    $this->subscriber->subscribe(
                        $email,
                        $language,
                        [],
                        [],
                        false // will ignore double-optin and so subscribes the user immediately
                    );
                }
            } catch (NotImplementedException $e) {
                // We do nothing as fallback when no mail-engine is chosen in the Backend
            }
        }
    }
}
