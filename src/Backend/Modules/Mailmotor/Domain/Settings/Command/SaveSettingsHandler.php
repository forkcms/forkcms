<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
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

    public function __construct(
        ModulesSettings $modulesSettings
    ) {
        $this->modulesSettings = $modulesSettings;
    }

    public function handle(SaveSettings $settings): void
    {
        // Define module
        $module = 'Mailmotor';

        // set our settings
        $this->modulesSettings->set($module, 'mail_engine', $settings->mailEngine);
        $this->modulesSettings->set($module, 'double_opt_in', $settings->doubleOptIn);
        $this->modulesSettings->set($module, 'overwrite_interests', $settings->overwriteInterests);
        $this->modulesSettings->set($module, 'automatically_subscribe_from_form_builder_submitted_form', $settings->automaticallySubscribeFromFormBuilderSubmittedForm);

        // mail engine is empty
        if ($settings->mailEngine === 'not_implemented') {
            $this->modulesSettings->delete($module, 'api_key');
            $this->modulesSettings->delete($module, 'list_id');

            return;
        }

        $this->modulesSettings->set($module, 'api_key', $settings->apiKey);
        $this->modulesSettings->set($module, 'list_id', $settings->listId);
    }
}
