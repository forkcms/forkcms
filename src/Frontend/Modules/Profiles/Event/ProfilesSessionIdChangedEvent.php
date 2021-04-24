<?php

namespace Frontend\Modules\Profiles\Event;

use Symfony\Component\EventDispatcher\Event;
use Frontend\Core\Engine\Model as FrontendModel;

class ProfilesSessionIdChangedEvent extends Event
{
    protected $sessionId;

    public function __construct()
    {
        $this->sessionId = FrontendModel::getSession()->getId();
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }
}
