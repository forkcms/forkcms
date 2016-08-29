<?php

namespace Frontend\Modules\FormBuilder\EventListener;

use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Frontend\Modules\FormBuilder\Engine\Model as FrontendFormBuilderModel;

/**
 * A Formbuilder submitted event subscriber that will send a notification
 *
 * @deprecated: no more support for the Fork-app.
 */
final class FormBuilderSubmittedNotificationSubscriber
{
    /**
     * @param FormBuilderSubmittedEvent $event
     */
    public function onFormSubmitted(FormBuilderSubmittedEvent $event)
    {
        $form = $event->getForm();
        FrontendFormBuilderModel::notifyAdmin(
            array(
                'form_id' => $form['id'],
                'entry_id' => $event->getDataId(),
            )
        );
    }
}
