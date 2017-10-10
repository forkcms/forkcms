<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

use Backend\Core\Language\Language;
use Common\ModulesSettings;

final class SaveSettingsHandler
{
    private const MODULE_NAME = 'Mailmotor';

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
        $this->modulesSettings->set(self::MODULE_NAME, 'mail_engine', $settings->mailEngine);
        $this->modulesSettings->set(self::MODULE_NAME, 'double_opt_in', $settings->doubleOptIn);
        $this->modulesSettings->set(self::MODULE_NAME, 'overwrite_interests', $settings->overwriteInterests);

        // mail engine is empty
        if ($settings->mailEngine === 'not_implemented') {
            $this->modulesSettings->delete(self::MODULE_NAME, 'api_key');
            $this->modulesSettings->delete(self::MODULE_NAME, 'list_id');

            foreach (Language::getActiveLanguages() as $language) {
                $this->modulesSettings->delete(self::MODULE_NAME, 'list_id_' . $language);
            }

            return;
        }

        $this->modulesSettings->set(self::MODULE_NAME, 'api_key', $settings->apiKey);
        $this->modulesSettings->set(self::MODULE_NAME, 'list_id', $settings->listId);
        $this->saveLanguageListIds($settings->languageListIds);
    }

    private function saveLanguageListIds(array $languageListIds): void
    {
        foreach ($languageListIds as $language => $languageListId) {
            if (empty($languageListId) || !Language::isActiveLanguage($language)) {
                $this->modulesSettings->delete(self::MODULE_NAME, 'list_id_' . $language);

                continue;
            }

            $this->modulesSettings->set(self::MODULE_NAME, 'list_id_' . $language, $languageListId);
        }
    }
}
