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
        $settings = $modulesSettings->getForModule('Mailmotor');
        $this->mailEngine = $settings['mail_engine'] ?? null;
        $this->apiKey = $settings['api_key'] ?? null;
        $this->listId = $settings['list_id'] ?? null;
        $this->overwriteInterests = (bool) ($settings['overwrite_interests'] ?? false);
        $this->doubleOptIn = (bool) ($settings['double_opt_in'] ?? false);
        $this->automaticallySubscribeFromFormBuilderSubmittedForm = (bool) (
            $settings['automatically_subscribe_from_form_builder_submitted_form'] ?? false
        );
    }
}
