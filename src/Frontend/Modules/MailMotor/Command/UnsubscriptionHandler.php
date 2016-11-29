<?php

namespace Frontend\Modules\MailMotor\Command;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;

final class UnsubscriptionHandler
{
    /**
     * @var Subscriber
     */
    protected $subscriber;

    /**
     * UnsubscriptionHandler constructor.
     *
     * @param Subscriber $subscriber
     */
    public function __construct(
        Subscriber $subscriber
    ) {
        $this->subscriber = $subscriber;
    }

    /**
     * Handle
     *
     * @param Unsubscription $unsubscription
     */
    public function handle(Unsubscription $unsubscription)
    {
        // Unsubscribing the user, will dispatch an event
        $this->subscriber->unsubscribe(
            $unsubscription->email
        );
    }
}
