<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription\Command;

use Common\ModulesSettings;
use Frontend\Core\Language\Locale;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;

final class UnsubscriptionHandler
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

    public function handle(Unsubscription $unsubscription): void
    {
        // Unsubscribing the user, will dispatch an event
        $this->subscriber->unsubscribe(
            $unsubscription->email,
            $this->modulesSettings->get('Mailmotor', 'list_id_' . Locale::frontendLanguage())
        );
    }
}
