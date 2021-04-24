<?php

namespace Frontend\Modules\Profiles\Event;

use Symfony\Component\EventDispatcher\Event;
use Frontend\Core\Engine\Model as FrontendModel;

class ProfilesSessionIdChangedEvent extends Event
{
    /**
     * @var string
     */
    protected $oldSessionId;

    /**
     * @var string
     */
    protected $sessionId;

    public function __construct(string $oldSessionId)
    {
        $this->oldSessionId = $oldSessionId;
        $this->sessionId = FrontendModel::getSession()->getId();
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
