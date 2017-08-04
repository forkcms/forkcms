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
use Backend\Modules\Mailmotor\Domain\Settings\SettingsType;

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
