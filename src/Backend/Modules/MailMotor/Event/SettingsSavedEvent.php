<?php

namespace Backend\Modules\MailMotor\Event;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Modules\MailMotor\Command\SaveSettings;
use Symfony\Component\EventDispatcher\Event;

/**
 * MailMotor settings saved Event
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

    /**
     * Construct
     *
     * @param SaveSettings $settings
     */
    public function __construct(
        SaveSettings $settings
    ) {
        $this->settings = $settings;
    }

    /**
     * Get settings
     *
     * @return SaveSettings
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
