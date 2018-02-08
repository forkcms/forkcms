<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use App\Service\Module\ModuleSettings;
use App\Component\Locale\FrontendLocale;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;

final class SubscriptionHandler
{
    /**
     * @var ModuleSettings
     */
    private $moduleSettings;

    /**
     * @var Subscriber
     */
    private $subscriber;

    public function __construct(Subscriber $subscriber, ModuleSettings $moduleSettings)
    {
        $this->subscriber = $subscriber;
        $this->moduleSettings = $moduleSettings;
    }

    public function handle(Subscription $subscription): void
    {
        $mergeFields = [];
        $interests = [];
        $languageSpecificListId = $this->moduleSettings->get('Mailmotor', 'list_id_' . FrontendLocale::frontendLanguage());

        try {
            if ($this->moduleSettings->get('Mailmotor', 'overwrite_interests', true)) {
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
            $this->moduleSettings->get('Mailmotor', 'double_opt_in', true),
            $languageSpecificListId
        );
    }
}
