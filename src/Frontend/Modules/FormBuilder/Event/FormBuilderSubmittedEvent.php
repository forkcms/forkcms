<?php

namespace Frontend\Modules\FormBuilder\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * This class is in fact an immutable event class holding all the data
 * that could be needed by event subscribers on the FormBuilder submitted event
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
     * @param int $dataId
     *
     * @internal param int $datId
     */
    public function __construct(array $form, array $data, int $dataId)
    {
        $this->form = $form;
        $this->data = $data;
        $this->dataId = $dataId;
    }

    /**
     * @return array
     */
    public function getForm(): array
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getDataId(): int
    {
        return $this->dataId;
    }
}
