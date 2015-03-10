<?php

namespace Frontend\Modules\FormBuilder\EventListener;

use Swift_Mailer;
use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Frontend\Modules\FormBuilder\Engine\Model as FrontendFormBuilderModel;

class FormBuilderSubmittedNotificationSubscriber
{
    public function onFormSubmitted(FormBuilderSubmittedEvent $event)
    {
        // notify the admin
        $form = $event->getForm();
        FrontendFormBuilderModel::notifyAdmin(
            array(
                'form_id' => $form['id'],
                'entry_id' => $event->getDataId(),
            )
        );
    }
}
