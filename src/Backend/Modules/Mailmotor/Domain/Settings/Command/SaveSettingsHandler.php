<?php

namespace Backend\Modules\Mailmotor\Domain\Settings\Command;

use App\Component\Locale\BackendLanguage;
use App\Service\Module\ModuleSettings;

final class SaveSettingsHandler
{
    private const MODULE_NAME = 'Mailmotor';

    /**
     * @var ModuleSettings
     */
    private $moduleSettings;

    public function __construct(
        ModuleSettings $moduleSettings
    ) {
        $this->moduleSettings = $moduleSettings;
    }

    public function handle(SaveSettings $settings): void
    {
        $this->moduleSettings->set(self::MODULE_NAME, 'mail_engine', $settings->mailEngine);
        $this->moduleSettings->set(self::MODULE_NAME, 'double_opt_in', $settings->doubleOptIn);
        $this->moduleSettings->set(self::MODULE_NAME, 'overwrite_interests', $settings->overwriteInterests);

        // mail engine is empty
        if ($settings->mailEngine === 'not_implemented') {
            $this->moduleSettings->delete(self::MODULE_NAME, 'api_key');
            $this->moduleSettings->delete(self::MODULE_NAME, 'list_id');

            foreach (BackendLanguage::getActiveLanguages() as $language) {
                $this->moduleSettings->delete(self::MODULE_NAME, 'list_id_' . $language);
            }

            return;
        }

        $this->moduleSettings->set(self::MODULE_NAME, 'api_key', $settings->apiKey);
        $this->moduleSettings->set(self::MODULE_NAME, 'list_id', $settings->listId);
        $this->saveLanguageListIds($settings->languageListIds);
    }

    private function saveLanguageListIds(array $languageListIds): void
    {
        foreach ($languageListIds as $language => $languageListId) {
            if (empty($languageListId) || !BackendLanguage::isActiveLanguage($language)) {
                $this->moduleSettings->delete(self::MODULE_NAME, 'list_id_' . $language);

                continue;
            }

            $this->moduleSettings->set(self::MODULE_NAME, 'list_id_' . $language, $languageListId);
        }
    }
}
