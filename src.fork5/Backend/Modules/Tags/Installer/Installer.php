<?php

namespace Backend\Modules\Tags\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Tags\Domain\ModuleTag\ModuleTag;
use Backend\Modules\Tags\Domain\Tag\Tag;
use Common\Doctrine\Entity\CreateSchema;
use ForkCMS\Bundle\InstallerBundle\Language\Locale;

/**
 * Installer for the tags module
 */
class Installer extends ModuleInstaller
{
    /** @var int */
    private $tagsBlockId;

    public function install(): void
    {
        $this->addModule('Tags');
        $this->configureEntities();
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'tags/index', ['tags/edit']);
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Autocomplete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'MassAction');
        $this->setActionRights(1, $this->getModule(), 'GetAllTags');
    }

    private function configureFrontendExtras(): void
    {
        $this->tagsBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), $this->getModule());
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'TagCloud', 'TagCloud');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Related', 'Related');
    }

    private function configureFrontendPages(): void
    {
        $searchId = $this->getSearchWidgetId();

        // loop languages
        foreach ($this->getLanguages() as $language) {
            if ($this->hasPageWithTagsBlock(Locale::fromString($language))) {
                continue;
            }

            // insert contact page
            $this->insertPage(
                [
                    'title' => $this->getModule(),
                    'type' => 'root',
                    'language' => $language,
                ],
                null,
                ['extra_id' => $this->tagsBlockId, 'position' => 'main'],
                ['extra_id' => $searchId, 'position' => 'top']
            );
        }
    }

    private function getSearchWidgetId(): int
    {
        /** @var ModuleExtraRepository $moduleExtraRepository */
        $moduleExtraRepository = Model::get(ModuleExtraRepository::class);
        $widgetId = $moduleExtraRepository->getModuleExtraId('Search', 'Form', ModuleExtraType::widget());

        if ($widgetId === null) {
            throw new \RuntimeException('Could not find Search Widget');
        }

        return $widgetId;
    }

    private function hasPageWithTagsBlock(Locale $locale): bool
    {
        return Model::getContainer()->get(PageBlockRepository::class)->moduleExtraExistsForLocale(
            $this->tagsBlockId,
            $locale
        );
    }

    private function configureEntities(): void
    {
        Model::get(CreateSchema::class)->forEntityClasses(
            [
                Tag::class,
                ModuleTag::class,
            ]
        );
    }
}
