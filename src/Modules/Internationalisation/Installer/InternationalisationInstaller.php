<?php

namespace ForkCMS\Modules\Internationalisation\Installer;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Installer\Domain\Configuration\InstallerConfiguration;
use ForkCMS\Modules\Internationalisation\Backend\Actions\ModuleSettings;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationAdd;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationDelete;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationEdit;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationExport;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationImport;
use ForkCMS\Modules\Internationalisation\Backend\Actions\TranslationIndex;
use ForkCMS\Modules\Internationalisation\Backend\Ajax\TranslationEdit as TranslationEditAjax;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleDataTransferObject;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class InternationalisationInstaller extends ModuleInstaller
{
    public const IS_REQUIRED = true;

    public function preInstall(): void
    {
        $this->createTableForEntities(Translation::class, InstalledLocale::class);
        $this->setInstalledLocales();
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();
        $this->configureBackendAjaxActions();
    }

    private function setInstalledLocales(): void
    {
        $installerConfiguration = InstallerConfiguration::fromCache();
        $defaults = [
            'isEnabledForWebsite' => true,
            'isDefaultForWebsite' => false,
            'isEnabledForUser' => false,
            'isDefaultForUser' => false,
        ];
        $localeConfig = array_fill_keys(
            array_map(static fn (Locale $locale): string => $locale->value, $installerConfiguration->getLocales()),
            $defaults
        );

        foreach ($installerConfiguration->getUserLocales() as $locale) {
            if (!array_key_exists($locale->value, $localeConfig)) {
                $localeConfig[$locale->value] = $defaults;
                $localeConfig[$locale->value]['isEnabledForWebsite'] = false;
            }

            $localeConfig[$locale->value]['isEnabledForUser'] = true;
        }

        $localeConfig[$installerConfiguration->getDefaultLocale()->value]['isDefaultForWebsite'] = true;
        $localeConfig[$installerConfiguration->getDefaultUserLocale()->value]['isDefaultForUser'] = true;

        foreach ($localeConfig as $locale => $config) {
            $installedLocale = new InstalledLocaleDataTransferObject();
            $installedLocale->locale = Locale::from($locale);
            $installedLocale->isDefaultForWebsite = $config['isDefaultForWebsite'];
            $installedLocale->isEnabledForWebsite = $config['isEnabledForWebsite'];
            $installedLocale->isEnabledForBrowserLocaleRedirect = $config['isEnabledForWebsite'];
            $installedLocale->isEnabledForUser = $config['isEnabledForUser'];
            $installedLocale->isDefaultForUser = $config['isDefaultForUser'];

            $this->installedLocaleRepository->save(InstalledLocale::fromDataTransferObject($installedLocale));
        }
    }

    private function createBackendPages(): void
    {
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Translations'),
            TranslationIndex::getActionSlug(),
            $this->getSettingsNavigationItem(),
            [
                TranslationAdd::getActionSlug(),
                TranslationEdit::getActionSlug(),
                TranslationDelete::getActionSlug(),
                TranslationImport::getActionSlug(),
                TranslationExport::getActionSlug(),
            ],
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Languages'),
            ModuleSettings::getActionSlug(),
            $this->getModuleSettingsNavigationItem()
        );
    }

    private function configureBackendAjaxActions(): void
    {
        $this->allowGroupToAccessModuleAjaxAction(TranslationEditAjax::getAjaxActionSlug()->asModuleAction());
    }
}
