<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

final class SubscriptionHandler
{
    /**
     * @var ModulesSettings
     */
    protected $modulesSettings;

    /**
     * @var Subscriber
     */
    protected $subscriber;

    public function __construct(
        Subscriber $subscriber,
        ModulesSettings $modulesSettings
    ) {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
    }

    public function handle(Subscription $subscription): void
    {
        // Init variables
        $mergeFields = [];
        $interests = [];

        try {
            // We must overwrite existing interests
            if ($this->modulesSettings->get('Mailmotor', 'overwrite_interests', true)) {
                $possibleInterests = $this->subscriber->getInterests();

                // Loop interests
                foreach ($possibleInterests as $categoryId => $categoryInterest) {
                    foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                        // Add interest
                        $interests[$categoryChildId] = in_array($categoryChildId, $subscription->interests);
                    }
                }
            } elseif (!empty($subscription->interests)) {
                // Loop checked interests
                foreach ($subscription->interests as $checkedInterestId) {
                    // Add interest
                    $interests[$checkedInterestId] = true;
                }
            }
        } catch (NotImplementedException $e) {
            // Fallback for when no mail-engine is chosen in the Backend
        }

        // Subscribing the user, will dispatch an event
        $this->subscriber->subscribe(
            $subscription->email,
            (string) $subscription->locale,
            $mergeFields,
            $interests,
            $this->modulesSettings->get('Mailmotor', 'double_opt_in', true)
        );
    }
}
