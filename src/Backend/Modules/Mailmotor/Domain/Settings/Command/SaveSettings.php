<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
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
     * @var bool
     */
    public $doubleOptIn;

    /**
     * @var bool
     */
    public $overwriteInterests;

    /**
     * @var bool
     */
    public $automaticallySubscribeFromFormBuilderSubmittedForm;

    public function __construct(ModulesSettings $modulesSettings)
    {
        // Define settings
        $settings = $modulesSettings->getForModule('Mailmotor');

        // Define mail engine
        $this->mailEngine = array_key_exists('mail_engine', $settings)
            ? $settings['mail_engine'] : null;

        // Define api key
        $this->apiKey = array_key_exists('api_key', $settings)
            ? $settings['api_key'] : null;

        // Define list id
        $this->listId = array_key_exists('list_id', $settings)
            ? $settings['list_id'] : null;

        // Define overwrite interests
        $this->overwriteInterests = array_key_exists('overwrite_interests', $settings)
            ? (bool) $settings['overwrite_interests'] : null;

        // Define double opt-in
        $this->doubleOptIn = (bool) $settings['double_opt_in'] ?? false;

        // Define automatically subscribe from form builder submitted form
        $this->automaticallySubscribeFromFormBuilderSubmittedForm = array_key_exists('automatically_subscribe_from_form_builder_submitted_form', $settings)
            ? (bool) $settings['automatically_subscribe_from_form_builder_submitted_form'] : false;
    }
}
