<?php

namespace Common\Events;

use Symfony\Component\EventDispatcher\Event;

class ForkSessionIdChangedEvent extends Event
{
    /**
     * @var string
     */
    protected $oldSessionId;

    /**
     * @var string
     */
    protected $sessionId;

    public function __construct(string $oldSessionId, string $sessionId)
    {
        $this->oldSessionId = $oldSessionId;
        $this->sessionId = $sessionId;
    }

    public function getOldSessionId()
    {
        return $this->oldSessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }
}
