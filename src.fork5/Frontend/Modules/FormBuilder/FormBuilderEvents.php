<?php

namespace Frontend\Modules\FormBuilder;

/**
 * A helper class that contains all possible formbuilder events
 */
final class FormBuilderEvents
{
    /**
     * The form.submitted event is thrown each time a formbuilder instance is
     * submitted.
     *
     * The event listener receives an
     * Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent instance.
     *
     * @var string
     */
    const FORM_SUBMITTED = 'form.submitted';
}
