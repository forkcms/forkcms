<?php

namespace ForkCMS\Modules\Pages\Installer;

use Doctrine\ORM\Query\ResultSetMapping;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplate;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocale;
use ForkCMS\Modules\Internationalisation\Domain\Locale\InstalledLocaleRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Pages\Backend\Actions\ModuleSettings;
use ForkCMS\Modules\Pages\Backend\Actions\PageAdd;
use ForkCMS\Modules\Pages\Backend\Actions\PageCopyToOtherLocale;
use ForkCMS\Modules\Pages\Backend\Actions\PageDelete;
use ForkCMS\Modules\Pages\Backend\Actions\PageEdit;
use ForkCMS\Modules\Pages\Backend\Actions\PageIndex;
use ForkCMS\Modules\Pages\Backend\Ajax\PageMove;
use ForkCMS\Modules\Pages\DependencyInjection\PagesRouteLoader;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Page\PageRepository;
use ForkCMS\Modules\Pages\Domain\Revision\Command\CreateRevision;
use ForkCMS\Modules\Pages\Domain\Revision\MenuType;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use ForkCMS\Modules\Pages\Domain\RevisionBlock\RevisionBlock;
use ForkCMS\Modules\Pages\Frontend\Widgets\Sitemap;

final class PagesInstaller extends ModuleInstaller
{
    public const IS_REQUIRED = true;

    public function preInstall(): void
    {
        $this->createTableForEntities(Page::class, Revision::class, RevisionBlock::class);
    }

    public function install(): void
    {
        $this->importTranslations(__DIR__ . '/../assets/installer/translations.xml');
        $this->createBackendPages();
        $this->configureBackendAjaxActions();
        $this->createFrontendPages();
        $this->defaultModuleSettings();
    }

    private function createBackendPages(): void
    {
        $this->getOrCreateBackendNavigationItem(
            label: TranslationKey::label('Pages'),
            slug: PageIndex::getActionSlug(),
            selectedFor: [
                PageAdd::getActionSlug(),
                PageEdit::getActionSlug(),
                PageDelete::getActionSlug(),
                PageCopyToOtherLocale::getActionSlug(),
            ],
            sequence: 1,
        );
        $this->getOrCreateBackendNavigationItem(
            TranslationKey::label('Pages'),
            ModuleSettings::getActionSlug(),
            $this->getModuleSettingsNavigationItem()
        );
    }

    private function createFrontendPages(): void
    {
        /** @var InstalledLocaleRepository $installedLocaleRepository */
        $installedLocaleRepository = $this->getRepository(InstalledLocale::class);
        /** @var ThemeTemplateRepository $themeTemplateRepository */
        $themeTemplateRepository = $this->getRepository(ThemeTemplate::class);
        $locales = $installedLocaleRepository->findInstalledLocales();
        $this->createPage(
            $locales,
            'lbl.Home',
            MenuType::MAIN,
            themeTemplate: $themeTemplateRepository->findActiveByName('Home')
        );
        $this->createPage($locales, 'lbl.Disclaimer', MenuType::FOOTER);
        $this->createPage(
            $locales,
            'lbl.Sitemap',
            MenuType::FOOTER,
            createRevisionCallback: function (Locale $locale, CreateRevision $revision): void {
                $revision->addBlock('main', $this->getOrCreateFrontendBlock(Sitemap::getModuleBlock()->getName()));
            }
        );
        $this->setPagesAutoIncrement(Page::PAGE_ID_404);
        $this->createPage($locales, '404', MenuType::ROOT);
        $this->setPagesAutoIncrement(Page::PAGE_ID_START);
    }

    /**
     * @param Locale[] $locales
     * @param ?callable(Locale, CreateRevision): void $createRevisionCallback
     */
    public function createPage(
        array $locales,
        string $title,
        MenuType $type,
        ?Page $parentPage = null,
        ?Page $page = null,
        ?callable $createRevisionCallback = null,
        ThemeTemplate $themeTemplate = null
    ): Page {
        static $defaultTemplate = null;
        /** @var ThemeTemplateRepository $themeTemplateRepository */
        $themeTemplateRepository = $this->getRepository(ThemeTemplate::class);
        /** @var PageRepository $pageRepository */
        $pageRepository = $this->getRepository(Page::class);
        if ($themeTemplate === null && $defaultTemplate === null) {
            $defaultTemplate = $themeTemplateRepository->findDefaultTemplate();
        }

        foreach ($locales as $locale) {
            if ($page === null) {
                $page = new Page($locale);
                $pageRepository->save($page);
            }
            $revision = CreateRevision::new(
                $page,
                $locale,
                $themeTemplate ?? $defaultTemplate,
                false
            );
            $revision->title = $this->trans($locale, $title);
            $revision->parentPage = $parentPage;
            $revision->meta = Meta::forName($revision->title);
            $revision->type = $type;
            $revision->settings['navigationTitle'] = $this->trans($locale, $title);

            if ($createRevisionCallback !== null) {
                $createRevisionCallback($locale, $revision);
            }

            $this->dispatchCommand($revision);
        }

        return $page;
    }

    private function setPagesAutoIncrement(int $startValue): void
    {
        $this->entityManager->createNativeQuery(
            'ALTER TABLE pages__page AUTO_INCREMENT=' . $startValue,
            new ResultSetMapping()
        )->execute();
    }

    private function configureBackendAjaxActions(): void
    {
        $this->allowGroupToAccessModuleAjaxAction(PageMove::getAjaxActionSlug()->asModuleAction());
    }

    private function defaultModuleSettings(): void
    {
        $this->setSetting('enabled_extensions', [PagesRouteLoader::FORMAT_DEFAULT]);
    }
}
