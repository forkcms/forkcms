<?php

namespace Backend\Modules\Mailmotor\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
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
        if ($this->ping()) {
            $this->redirect($this->getBackLink(['report' => 'successful-mail-engine-api-connection']));
        }

        $this->resetMailEngine();
        $this->redirect($this->getBackLink(['error' => 'wrong-mail-engine-credentials']));
    }

    private function ping(): bool
    {
        $gateway = $this->getContainer()->get('mailmotor.factory')->getSubscriberGateway();

        // don't try to ping if you aren't using a service like mailchimp or campaign monitor
        if (!$gateway->ping($this->getContainer()->getParameter('mailmotor.list_id'))) {
            return false;
        }

        $settings = $this->getContainer()->get('fork.settings');
        foreach (Language::getActiveLanguages() as $language) {
            $languageListId = $settings->get('Mailmotor', 'list_id_' . $language);

            if ($languageListId === null) {
                continue;
            }

            if (!$gateway->ping($languageListId)) {
                return false;
            }
        }

        return true;
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
