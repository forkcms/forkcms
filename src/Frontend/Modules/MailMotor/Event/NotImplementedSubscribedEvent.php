<?php

namespace Frontend\Modules\MailMotor\Event;

use Frontend\Modules\MailMotor\Command\Subscription;
use Symfony\Component\EventDispatcher\Event;

final class NotImplementedSubscribedEvent extends Event
{
    const EVENT_NAME = 'mailmotor.event.not_implemented.subscribed';

    /**
     * @var Subscription
     */
    protected $subscription;

    /**
     * NotImplementedSubscriptionEvent constructor.
     * @param Subscription $subscription
     */
    public function __construct(
        Subscription $subscription
    ) {
        $this->subscription = $subscription;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
}
