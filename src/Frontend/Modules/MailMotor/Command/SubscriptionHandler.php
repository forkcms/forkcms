<?php

namespace Frontend\Modules\MailMotor\Command;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
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

    /**
     * SubscriptionHandler constructor.
     *
     * @param Subscriber $subscriber
     * @param ModulesSettings $modulesSettings
     */
    public function __construct(
        Subscriber $subscriber,
        ModulesSettings $modulesSettings
    ) {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
    }

    /**
     * Handle
     *
     * @param Subscription $subscription
     */
    public function handle(Subscription $subscription)
    {
        // Init variables
        $mergeFields = array();
        $interests = array();

        try {
            // We must overwrite existing interests
            if ($this->modulesSettings->get('MailMotor', 'overwrite_interests', true)) {
                $possibleInterests = $this->subscriber->getInterests();

                // Loop interests
                foreach ($possibleInterests as $categoryId => $categoryInterest) {
                    foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                        // Add interest
                        $interests[$categoryChildId] = in_array($categoryChildId, $subscription->interests);
                    }
                }
            } else {
                // Loop checked interests
                foreach ($checkedInterests as $checkedInterestId) {
                    // Add interest
                    $interests[$checkedInterestId] = true;
                }
            }
        // Fallback for when no mail-engine is chosen in the Backend
        } catch (NotImplementedException $e) {

        }

        // Subscribing the user, will dispatch an event
        $this->subscriber->subscribe(
            $subscription->email,
            $subscription->language,
            $mergeFields,
            $interests
        );
    }
}
