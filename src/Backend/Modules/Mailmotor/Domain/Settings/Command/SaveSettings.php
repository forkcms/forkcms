<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

use Backend\Core\Language\Language;
use Common\ModulesSettings;
use Symfony\Component\Validator\Constraints as Assert;

final class SaveSettings
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"mail_engine_selected"}, message="err.FieldIsRequired")
     */
    public $apiKey;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"mail_engine_selected"}, message="err.FieldIsRequired")
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
     * @var array
     */
    public $languageListIds;

    public function __construct(ModulesSettings $modulesSettings)
    {
        $settings = $modulesSettings->getForModule('Mailmotor');
        $this->mailEngine = $settings['mail_engine'] ?? null;
        $this->apiKey = $settings['api_key'] ?? null;
        $this->listId = $settings['list_id'] ?? null;
        $this->overwriteInterests = (bool) ($settings['overwrite_interests'] ?? false);
        $this->doubleOptIn = (bool) ($settings['double_opt_in'] ?? false);
        $this->languageListIds = $this->setLanguageListIds($settings);
    }

    private function setLanguageListIds(array $settings): array
    {
        $languageListIds = [];
        foreach (Language::getActiveLanguages() as $language) {
            $languageListIds[$language] = $settings['list_id_' . $language] ?? null;
        }

        return $languageListIds;
    }
}
