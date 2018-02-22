<?php

namespace App\Backend\Modules\Mailmotor\Actions;

use App\Backend\Core\Engine\Base\ActionIndex;
use App\Backend\Core\Engine\Model;
use App\Backend\Modules\Mailmotor\Domain\Settings\Command\SaveSettings;
use App\Backend\Modules\Mailmotor\Domain\Settings\Event\SettingsSavedEvent;
use App\Backend\Modules\Mailmotor\Domain\Settings\SettingsType;

/**
 * This is the settings-action (default),
 * it will be used to couple your "mail-engine" account
 */
final class Settings extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();

        $form = $this->createForm(
            SettingsType::class,
            new SaveSettings($this->get('fork.settings'))
        );

        $form->handleRequest($this->getRequest());

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        /** @var SaveSettings $settings */
        $settings = $form->getData();

        // The command bus will handle the saving of the settings in the database.
        $this->get('command_bus')->handle($settings);

        $this->get('event_dispatcher')->dispatch(
            SettingsSavedEvent::EVENT_NAME,
            new SettingsSavedEvent($settings)
        );

        $redirectAction = $settings->mailEngine === 'not_implemented' ? 'Settings' : 'Ping';

        $this->redirect(
            Model::createUrlForAction(
                $redirectAction,
                null,
                null,
                [
                    'report' => 'saved',
                ]
            )
        );
    }
}
