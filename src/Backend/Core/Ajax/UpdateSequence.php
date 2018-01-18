<?php

namespace Backend\Core\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Symfony\Component\HttpFoundation\Response;

class UpdateSequence extends BackendBaseAJAXAction
{
    /**
     * @var string
     */
    private $handlerClass;

    public function execute(): void
    {
        if ($this->handlerClass === null) {
            throw new \Exception('The "UpdateSequence" AJAX class needs a command handler class. You must set it like this: `$this->addHandlerClass(CategorySequence::class)`.');
        }

        parent::execute();

        // get parameters
        $newIdSequence = trim($this->getRequest()->request->get('new_id_sequence'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // Handle the Categories ReSequence
        $this->get('command_bus')->handle(new $this->handlerClass($ids));

        // success output
        $this->output(Response::HTTP_OK, null, 'sequence updated');
    }

    public function setHandlerClass(string $handlerClass): void
    {
        $this->handlerClass = $handlerClass;
    }
}
