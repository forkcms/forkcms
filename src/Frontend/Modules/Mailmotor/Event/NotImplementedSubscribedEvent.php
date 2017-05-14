<?php

namespace Frontend\Modules\Mailmotor\Event;

use Frontend\Modules\Mailmotor\Command\Subscription;
use Symfony\Component\EventDispatcher\Event;

final class NotImplementedSubscribedEvent extends Event
{
    const EVENT_NAME = 'mailmotor.event.not_implemented.subscribed';

    /**
     * @var Subscription
     */
    protected $subscription;

    public function __construct(
        Subscription $subscription
    ) {
        $this->subscription = $subscription;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }
}
