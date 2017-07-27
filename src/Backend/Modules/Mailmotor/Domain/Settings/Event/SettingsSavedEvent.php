<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Event;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
