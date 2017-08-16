<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Modules\Mailmotor\Domain\Settings\Command\SaveSettings;
use Backend\Modules\Mailmotor\Domain\Settings\Event\SettingsSavedEvent;

/**
 * This tests the api
 */
final class Ping extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();

        // Successful API connection
        if ($this->get('mailmotor.subscriber')->ping()) {
            $this->redirect($this->getBackLink(['report' => 'successful-mail-engine-api-connection']));
        }

        $this->resetMailEngine();
        $this->redirect($this->getBackLink(['error' => 'wrong-mail-engine-credentials']));
    }

    private function getBackLink(array $parameters = []): string
    {
        return Model::createUrlForAction(
            'Settings',
            null,
            null,
            $parameters
        );
    }

    private function resetMailEngine(): void
    {
        $saveSettings = new SaveSettings($this->get('fork.settings'));
        $saveSettings->mailEngine = 'not_implemented';

        $this->get('command_bus')->handle($saveSettings);

        $this->get('event_dispatcher')->dispatch(
            SettingsSavedEvent::EVENT_NAME,
            new SettingsSavedEvent($saveSettings)
        );
    }
}
