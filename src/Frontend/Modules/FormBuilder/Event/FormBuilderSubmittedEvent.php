<?php

namespace Frontend\Modules\FormBuilder\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * This class is in fact an immutable event class holding all the data
 * that could be needed by event subscribers on the FormBuilder submitted event
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class FormBuilderSubmittedEvent extends Event
{
    /**
     * @var array
     */
    protected $form;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param int
     */
    protected $dataId;

    /**
     * @param array $form
     * @param array $data
     * @param $dataId
     *
     * @internal param int $datId
     */
    public function __construct($form, $data, $dataId)
    {
        $this->form = $form;
        $this->data = $data;
        $this->dataId = $dataId;
    }

    /**
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getDataId()
    {
        return $this->dataId;
    }
}
