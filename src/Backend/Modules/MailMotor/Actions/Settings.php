<?php

namespace Backend\Modules\MailMotor\Actions;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Model;
use Backend\Modules\MailMotor\Command\SaveSettings;
use Backend\Modules\MailMotor\Event\SettingsSavedEvent;

/**
 * This is the settings-action (default),
 * it will be used to couple your "mail-engine" account
 */
final class Settings extends ActionIndex
{
    /**
     * Execute
     */
    public function execute()
    {
        parent::execute();

        $form = $this->createForm(
            $this->get('mailmotor.form.settings'),
            new SaveSettings($this->get('fork.settings'))
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());

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

        return $this->redirect(
            Model::createURLForAction(
                'Settings',
                null,
                null,
                [
                    'report' => 'saved',
                ]
            )
        );
    }
}
