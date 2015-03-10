<?php

namespace Frontend\Modules\FormBuilder\Event;

use Symfony\Component\EventDispatcher\Event;

class FormBuilderSubmittedEvent extends Event
{
    protected $form;
    protected $data;
    protected $dataId;

    public function __construct($form, $data, $dataId)
    {
        $this->form = $form;
        $this->data = $data;
        $this->dataId = $dataId;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataId()
    {
        return $this->dataId;
    }
}
