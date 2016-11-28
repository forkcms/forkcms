<?php

namespace Backend\Modules\MailMotor\Command;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;
use Symfony\Component\Validator\Constraints as Assert;

final class SaveSettings
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"mail_engine_selected"})
     */
    public $apiKey;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"mail_engine_selected"})
     */
    public $listId;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $mailEngine;

    /**
     * @var boolean
     */
    public $overwriteInterests;

    /**
     * @var boolean
     */
    public $automaticallySubscribeFromFormBuilderSubmittedForm;

    /**
     * @param ModulesSettings $modulesSettings
     */
    public function __construct(ModulesSettings $modulesSettings)
    {
        // Define settings
        $settings = $modulesSettings->getForModule('MailMotor');

        // Define mail engine
        $this->mailEngine = isset($settings['mail_engine'])
            ? $settings['mail_engine'] : null;

        // Define api key
        $this->apiKey = isset($settings['api_key'])
            ? $settings['api_key'] : null;

        // Define list id
        $this->listId = isset($settings['list_id'])
            ? $settings['list_id'] : null;

        // Define overwrite interests
        $this->overwriteInterests = isset($settings['overwrite_interests'])
            ? (bool) $settings['overwrite_interests'] : null;

        // Define automatically subscribe from form builder submitted form
        $this->automaticallySubscribeFromFormBuilderSubmittedForm = isset($settings['automatically_subscribe_from_form_builder_submitted_form'])
            ? (bool) $settings['automatically_subscribe_from_form_builder_submitted_form'] : false;
    }
}
