<?php

namespace Backend\Modules\Mailmotor\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use ForkCMS\Bundle\InstallerBundle\Language\Locale;

/**
 * Installer for the Mailmotor module
 */
class Installer extends ModuleInstaller
{
    /** @var int */
    private $subscribeBlockId;

    /** @var int */
    private $unsubscribeBlockId;

    public function install(): void
    {
        $this->addModule('Mailmotor');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'mailmotor/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Ping');
        $this->setActionRights(1, $this->getModule(), 'Settings');
        $this->setActionRights(1, $this->getModule(), 'Index');
    }

    private function configureFrontendExtras(): void
    {
        $this->subscribeBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'SubscribeForm', 'Subscribe');
        $this->unsubscribeBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), 'UnsubscribeForm', 'Unsubscribe');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'SubscribeForm', 'Subscribe');
    }

    private function configureFrontendPages(): void
    {
        // loop languages
        foreach ($this->getLanguages() as $language) {
            $pageId = $this->getPageWithMailmotorBlock($language);
            if ($pageId === null) {
                $pageId = $this->insertPage(
                    ['title' => 'Newsletters', 'language' => $language]
                );
            }

            if (!$this->hasPageWithSubscribeBlock($language)) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Subscribe', 'language' => $language],
                    null,
                    ['extra_id' => $this->subscribeBlockId, 'position' => 'main']
                );
            }

            if (!$this->hasPageWithUnsubscribeBlock($language)) {
                $this->insertPage(
                    ['parent_id' => $pageId, 'title' => 'Unsubscribe', 'language' => $language],
                    null,
                    ['extra_id' => $this->unsubscribeBlockId, 'position' => 'main']
                );
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'api_key', null);
        $this->setSetting($this->getModule(), 'double_opt_in', true);
        $this->setSetting($this->getModule(), 'list_id', null);
        $this->setSetting($this->getModule(), 'mail_engine', null);
        $this->setSetting($this->getModule(), 'overwrite_interests', false);
    }

    private function hasPageWithSubscribeBlock(string $language): bool
    {
        return Model::getContainer()->get(PageBlockRepository::class)->moduleExtraExistsForLocale(
            $this->subscribeBlockId,
            Locale::fromString($language)
        );
    }

    private function hasPageWithUnsubscribeBlock(string $language): bool
    {
        return Model::getContainer()->get(PageBlockRepository::class)->moduleExtraExistsForLocale(
            $this->unsubscribeBlockId,
            Locale::fromString($language)
        );
    }

    private function getPageWithMailmotorBlock(string $language): ?int
    {
        $page = Model::getContainer()->get(PageRepository::class)->findOneBy(
            ['title' => 'Newsletters', 'locale' => Locale::fromString($language)]
        );

        if ($page instanceof Page) {
            return $page->getId();
        }

        return null;
    }
}
