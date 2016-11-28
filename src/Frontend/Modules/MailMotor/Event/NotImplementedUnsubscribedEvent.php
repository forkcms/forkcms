<?php

namespace Frontend\Modules\MailMotor\Event;

use Frontend\Modules\MailMotor\Command\Unsubscription;
use Symfony\Component\EventDispatcher\Event;

final class NotImplementedUnsubscribedEvent extends Event
{
    const EVENT_NAME = 'mailmotor.event.not_implemented.unsubscribed';

    /**
     * @var Unsubscription
     */
    protected $unsubscription;

    /**
     * NotImplementedSubscriptionEvent constructor.
     * @param Unsubscription $unsubscription
     */
    public function __construct(
        Unsubscription $unsubscription
    ) {
        $this->unsubscription = $unsubscription;
    }

    /**
     * @return Unsubscription
     */
    public function getUnsubscription()
    {
        return $this->unsubscription;
    }
}
