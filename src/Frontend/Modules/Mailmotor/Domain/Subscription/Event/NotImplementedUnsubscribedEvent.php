<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Event;

use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Unsubscription;
use Symfony\Component\EventDispatcher\Event;

final class NotImplementedUnsubscribedEvent extends Event
{
    const EVENT_NAME = 'mailmotor.event.not_implemented.unsubscribed';

    /**
     * @var Unsubscription
     */
    protected $unsubscription;

    public function __construct(
        Unsubscription $unsubscription
    ) {
        $this->unsubscription = $unsubscription;
    }

    public function getUnsubscription(): Unsubscription
    {
        return $this->unsubscription;
    }
}
