<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Language\Language;
use Common\ModulesSettings;

final class SaveSettingsHandler
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    public function __construct(
        ModulesSettings $modulesSettings
    ) {
        $this->modulesSettings = $modulesSettings;
    }

    public function handle(SaveSettings $settings): void
    {
        $this->saveSetting('mail_engine', $settings->mailEngine);
        $this->saveSetting('double_opt_in', $settings->doubleOptIn);
        $this->saveSetting('overwrite_interests', $settings->overwriteInterests);
        $this->saveSetting(
            'automatically_subscribe_from_form_builder_submitted_form',
            $settings->automaticallySubscribeFromFormBuilderSubmittedForm
        );

        // mail engine is empty
        if ($settings->mailEngine === 'not_implemented') {
            $this->deleteSetting('api_key');
            $this->deleteSetting('list_id');

            return;
        }

        $this->saveSetting('api_key', $settings->apiKey);
        $this->saveSetting('list_id', $settings->listId);
        $this->saveLanguageListIds($settings->languageListIds);
    }

    private function saveSetting(string $key, $value): void
    {
        $this->modulesSettings->set('Mailmotor', $key, $value);
    }

    private function deleteSetting(string $key): void
    {
        $this->modulesSettings->delete('Mailmotor', $key);
    }

    public function isActiveLanguage(string $language): bool
    {
        return in_array($language, Language::getActiveLanguages(), true);
    }

    private function saveLanguageListIds(array $languageListIds): void
    {
        foreach ($languageListIds as $language => $languageListId) {
            if (empty($languageListId) || !$this->isActiveLanguage($language)) {
                $this->deleteSetting('list_id_' . $language);

                continue;
            }

            $this->saveSetting('list_id_' . $language, $languageListId);
        }
    }
}
