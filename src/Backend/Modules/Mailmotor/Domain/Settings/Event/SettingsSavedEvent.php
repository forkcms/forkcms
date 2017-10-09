<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Event;

use Backend\Modules\Mailmotor\Domain\Settings\Command\SaveSettings;
use Symfony\Component\EventDispatcher\Event;

/**
 * Mailmotor settings saved Event
 */
final class SettingsSavedEvent extends Event
{
    /**
     * @var string The name the listener needs to listen to to catch this event.
     */
    const EVENT_NAME = 'mailmotor.event.settings_saved';

    /**
     * @var SaveSettings
     */
    protected $settings;

    public function __construct(SaveSettings $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings(): SaveSettings
    {
        return $this->settings;
    }
}
