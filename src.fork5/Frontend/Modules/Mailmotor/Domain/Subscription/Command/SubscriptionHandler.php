<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use Common\ModulesSettings;
use Frontend\Core\Language\Locale;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

final class SubscriptionHandler
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    /**
     * @var Subscriber
     */
    private $subscriber;

    public function __construct(Subscriber $subscriber, ModulesSettings $modulesSettings)
    {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
    }

    public function handle(Subscription $subscription): void
    {
        $mergeFields = [];
        $interests = [];
        $languageSpecificListId = $this->modulesSettings->get('Mailmotor', 'list_id_' . Locale::frontendLanguage());

        try {
            if ($this->modulesSettings->get('Mailmotor', 'overwrite_interests', true)) {
                $possibleInterests = $this->subscriber->getInterests($languageSpecificListId);

                foreach ($possibleInterests as $categoryId => $categoryInterest) {
                    foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                        $interests[$categoryChildId] = in_array($categoryChildId, $subscription->interests);
                    }
                }
            } elseif (!empty($subscription->interests)) {
                foreach ($subscription->interests as $checkedInterestId) {
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
            $this->modulesSettings->get('Mailmotor', 'double_opt_in', true),
            $languageSpecificListId
        );
    }
}
