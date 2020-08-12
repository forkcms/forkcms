<?php

namespace Backend\Core\Engine\Base;

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the delete action
 */
class ActionDelete extends Action
{
    /**
     * The id of the item to edit
     *
     * @var int
     */
    protected $id;

    /**
     * The data of the item to edit
     *
     * @var array
     */
    protected $record;

    /**
     * Execute the current action
     * This method will be overwritten in most of the actions, but still be called to add general stuff
     */
    public function execute(): void
    {
        $this->parse();
        $this->checkToken();
    }
}
