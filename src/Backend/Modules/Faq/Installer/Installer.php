<?php

namespace Backend\Modules\Faq\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Faq\Domain\Category\Category;
use Backend\Modules\Faq\Domain\Feedback\Feedback;
use Backend\Modules\Faq\Domain\Question\Question;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Common\Doctrine\Entity\CreateSchema;
use ForkCMS\Bundle\InstallerBundle\Language\Locale;

/**
 * Installer for the faq module
 */
class Installer extends ModuleInstaller
{
    /** @var int */
    private $defaultCategoryId;

    /** @var int */
    private $faqBlockId;

    public function install(): void
    {
        $this->addModule('Faq');
        $this->makeSearchable($this->getModule());
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->configureEntities();
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
        $this->configureBackendWidgets();
        $this->configureFrontendExtras();
        $this->configureFrontendPages();
    }

    private function configureBackendActionRightsForFaqCategory(): void
    {
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'Sequence'); // AJAX
    }

    private function configureBackendActionRightsForFaqQuestion(): void
    {
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'SequenceQuestions'); // AJAX
    }

    private function configureBackendActionRightsForFaqQuestionFeedback(): void
    {
        $this->setActionRights(1, $this->getModule(), 'DeleteFeedback');
    }

    private function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationFaqId = $this->setNavigation($navigationModulesId, $this->getModule());
        $this->setNavigation(
            $navigationFaqId,
            'Questions',
            'faq/index',
            ['faq/add', 'faq/edit']
        );
        $this->setNavigation(
            $navigationFaqId,
            'Categories',
            'faq/categories',
            ['faq/add_category', 'faq/edit_category']
        );

        // Set navigation for "Settings"
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'faq/settings');
    }

    private function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        // Configure backend rights for entities
        $this->configureBackendActionRightsForFaqCategory();
        $this->configureBackendActionRightsForFaqQuestion();
        $this->configureBackendActionRightsForFaqQuestionFeedback();

        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function configureBackendWidgets(): void
    {
        $this->insertDashboardWidget($this->getModule(), 'Feedback');
    }

    /**
     * Configure frontend extras
     * Note: Category faq widgets will be added on the fly
     */
    private function configureFrontendExtras(): void
    {
        $this->faqBlockId = $this->insertExtra($this->getModule(), ModuleExtraType::block(), $this->getModule());
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'MostReadQuestions', 'MostReadQuestions');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'AskOwnQuestion', 'AskOwnQuestion');
        $this->insertExtra($this->getModule(), ModuleExtraType::widget(), 'Categories', 'Categories');
    }

    private function configureFrontendPages(): void
    {
        foreach ($this->getLanguages() as $language) {
            $this->defaultCategoryId = $this->getDefaultCategoryIdForLanguage($language);

            // no category exists
            if ($this->defaultCategoryId === 0) {
                $this->defaultCategoryId = $this->insertCategory($language, 'Default', 'default');
            }

            // check if a page for the faq already exists in this language
            $faqPageExists = Model::getContainer()->get(PageBlockRepository::class)->moduleExtraExistsForLocale(
                $this->faqBlockId,
                Locale::fromString($language)
            );

            if (!$faqPageExists) {
                // insert page
                $this->insertPage(
                    [
                        'title' => 'FAQ',
                        'language' => $language,
                    ],
                    null,
                    ['extra_id' => $this->faqBlockId]
                );
            }
        }
    }

    private function configureSettings(): void
    {
        $this->setSetting($this->getModule(), 'allow_feedback', false);
        $this->setSetting($this->getModule(), 'allow_multiple_categories', true);
        $this->setSetting($this->getModule(), 'allow_own_question', false);
        $this->setSetting($this->getModule(), 'most_read_num_items', 5);
        $this->setSetting($this->getModule(), 'overview_num_items_per_category', 10);
        $this->setSetting($this->getModule(), 'related_num_items', 5);
        $this->setSetting($this->getModule(), 'send_email_on_new_feedback', false);
        $this->setSetting($this->getModule(), 'spamfilter', false);
    }

    private function getDefaultCategoryIdForLanguage(string $language): int
    {
        return (int) $this->getDatabase()->getVar(
            'SELECT id
             FROM FaqCategory
             WHERE locale = ?',
            [$language]
        );
    }

    /**
     * @todo: When FAQ entities are available, use DataFixtures instead of this method.
     *
     * @param string $language
     * @param string $title
     * @param string $url
     *
     * @return int
     */
    private function insertCategory(string $language, string $title, string $url): int
    {
        $database = $this->getDatabase();

        // get sequence for widget
        /** @var ModuleExtraRepository $moduleExtraRepository */
        $moduleExtraRepository = BackendModel::getContainer()->get(ModuleExtraRepository::class);
        $sequenceExtra = $moduleExtraRepository->getNextSequenceByModule('Faq');

        $moduleExtra = new ModuleExtra(
            $this->getModule(),
            ModuleExtraType::widget(),
            $this->getModule(),
            'CategoryList',
            null,
            false,
            $sequenceExtra
        );

        $moduleExtraRepository->add($moduleExtra);
        $moduleExtraRepository->save($moduleExtra);

        // build array
        $item = [
            'meta_id' => $this->insertMeta($title, $title, $title, $url),
            'extraId' => $moduleExtra->getId(),
            'locale' => $language,
            'title' => $title,
            'sequence' => 1,
        ];

        // insert category
        $item['id'] = (int) $database->insert('FaqCategory', $item);

        // Update data with the
        $moduleExtra->update(
            $this->getModule(),
            ModuleExtraType::widget(),
            $this->getModule(),
            'CategoryList',
            [
                'id' => $item['id'],
                'extra_label' => 'Category: ' . $item['title'],
                    'language' => $item['locale'],
                'edit_url' => '/private/' . $language . '/faq/edit_category?id=' . $item['id'],
            ],
            false,
            $sequenceExtra
        );

        $moduleExtraRepository->save($moduleExtra);

        return $item['id'];
    }

    private function configureEntities(): void
    {
        Model::get(CreateSchema::class)->forEntityClasses(
            [
                Category::class,
                Question::class,
                Feedback::class,
            ]
        );
    }
}
