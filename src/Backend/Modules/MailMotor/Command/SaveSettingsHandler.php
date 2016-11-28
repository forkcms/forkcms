<?php

namespace Backend\Modules\MailMotor\Command;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;

final class SaveSettingsHandler
{
    /**
     * @var ModulesSettings
     */
    protected $modulesSettings;

    /**
     * SaveSettingsHandler constructor.
     *
     * @param ModulesSettings $modulesSettings
     */
    public function __construct(
        ModulesSettings $modulesSettings
    ) {
        $this->modulesSettings = $modulesSettings;
    }

    /**
     * @param SaveSettings $settings
     */
    public function handle(SaveSettings $settings)
    {
        // Define module
        $module = 'MailMotor';

        // set our settings
        $this->modulesSettings->set($module, 'mail_engine', $settings->mailEngine);
        $this->modulesSettings->set($module, 'overwrite_interests', $settings->overwriteInterests);
        $this->modulesSettings->set($module, 'automatically_subscribe_from_form_builder_submitted_form', $settings->automaticallySubscribeFromFormBuilderSubmittedForm);

        // mail engine is empty
        if ($settings->mailEngine == '') {
            $this->modulesSettings->delete($module, 'api_key');
            $this->modulesSettings->delete($module, 'list_id');
        } else {
            $this->modulesSettings->set($module, 'api_key', $settings->apiKey);
            $this->modulesSettings->set($module, 'list_id', $settings->listId);
        }
    }
}
