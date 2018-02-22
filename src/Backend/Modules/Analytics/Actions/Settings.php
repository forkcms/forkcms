<?php

namespace App\Backend\Modules\Analytics\Actions;

use App\Backend\Core\Engine\Base\ActionIndex;
use App\Backend\Core\Engine\Model;
use App\Backend\Modules\Analytics\Form\SettingsType;

/**
 * This is the settings-action (default), it will be used to couple your analytics
 * account
 */
final class Settings extends ActionIndex
{
    public function execute(): void
    {
        parent::execute();

        $settingsForm = new SettingsType(
            'settings',
            $this->get('fork.settings'),
            $this->get('analytics.google_analytics_service')
        );

        if ($settingsForm->handle()) {
            $this->redirect(Model::createUrlForAction('Settings'));
        }
        $settingsForm->parse($this->template);

        if ($this->get('fork.settings')->get($this->getModule(), 'web_property_id')) {
            $this->template->assign(
                'web_property_id',
                $this->get('fork.settings')->get($this->getModule(), 'web_property_id')
            );
        }
        if ($this->get('fork.settings')->get($this->getModule(), 'profile')) {
            $this->template->assign(
                'profile',
                $this->get('fork.settings')->get($this->getModule(), 'profile')
            );
        }

        $this->display();
    }
}
