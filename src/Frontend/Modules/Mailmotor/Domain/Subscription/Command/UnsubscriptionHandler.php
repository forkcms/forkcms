<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use App\Service\Module\ModuleSettings;
use App\Component\Locale\FrontendLocale;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;

final class UnsubscriptionHandler
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

    public function handle(Unsubscription $unsubscription): void
    {
        // Unsubscribing the user, will dispatch an event
        $this->subscriber->unsubscribe(
            $unsubscription->email,
            $this->moduleSettings->get('Mailmotor', 'list_id_' . FrontendLocale::frontendLanguage())
        );
    }
}
