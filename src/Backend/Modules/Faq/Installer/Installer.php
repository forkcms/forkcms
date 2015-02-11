<?php

namespace Backend\Modules\Faq\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Faq\Entity\Category;
use Backend\Modules\Faq\Engine\Model as BackendFaqModel;

/**
 * Installer for the faq module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Installer extends ModuleInstaller
{
    /**
     * @var	Category
     */
    private $defaultCategory;

    /**
     * Add a category for a language
     *
     * @param string $language
     * @param string $title
     * @param string $url
     * @return int
     */
    private function addCategory($language, $title, $url)
    {
        // db
        $db = $this->getDB();

        // get sequence for widget
        $sequenceExtra = $db->getVar(
            'SELECT MAX(i.sequence) + 1
             FROM modules_extras AS i
             WHERE i.module = ?',
            array($this->getModule())
        );

        // build category
        $category = new Category();
        $category
            ->setMeta($this->getMetaEntity($title, $title, $title, $url))
            ->setExtraId($this->insertExtra($this->getModule(), 'widget', $this->getModule(), 'CategoryList', null, 'N', $sequenceExtra))
            ->setLanguage((string) $language)
            ->setTitle((string) $title)
            ->setSequence(1)
        ;

        // insert category
        $em = BackendModel::get('doctrine.orm.entity_manager');
        $em->persist($category);
        $em->flush();

        // build data for widget
        $extra['data'] = serialize(
            array(
                'id' => $category->getId(),
                'extra_label' => 'Category: ' . $category->getTitle(),
                'language' => $language,
                'edit_url' => '/private/' . $language . '/faq/edit_category?id=' . $category->getId()
            )
        );

        // update widget
        $db->update(
            'modules_extras',
            $extra,
            'id = ? AND module = ? AND type = ? AND action = ?',
            array($category->getExtraId(), $this->getModule(), 'widget', 'CategoryList')
        );

        return $category->getId();
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language
     * @return Category|null
     */
    private function getCategory($language)
    {
        return BackendModel::get('doctrine.orm.entity_manager')
            ->getRepository(BackendFaqModel::CATEGORY_ENTITY_CLASS)
            ->findOneBy(
                array(
                    'language' => $language,
                )
            )
        ;
    }

    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        $feedback = array(
            'column' => 'right',
            'position' => 1,
            'hidden' => false,
            'present' => true
        );

        $this->insertDashboardWidget($this->getModule(), 'Feedback', $feedback);
    }

    /**
     * Install the module
     */
    public function install()
    {
        $this->addModule('Faq');

        $this->addEntitiesInDatabase(array(
            BackendFaqModel::CATEGORY_ENTITY_CLASS,
            BackendFaqModel::QUESTION_ENTITY_CLASS,
            BackendFaqModel::FEEDBACK_ENTITY_CLASS,
        ));

        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->makeSearchable($this->getModule());
        $this->setRights();

        $faqId = $this->insertExtra($this->getModule(), 'block', $this->getModule());

        // Register widgets
        // Category faq widgets will be added on the fly
        $this->insertExtra($this->getModule(), 'widget', 'MostReadQuestions', 'MostReadQuestions');
        $this->insertExtra($this->getModule(), 'widget', 'AskOwnQuestion', 'AskOwnQuestion');
        $this->insertExtra($this->getModule(), 'widget', 'Categories', 'Categories');

        $this->setSetting($this->getModule(), 'overview_num_items_per_category', 0);
        $this->setSetting($this->getModule(), 'most_read_num_items', 0);
        $this->setSetting($this->getModule(), 'related_num_items', 0);
        $this->setSetting($this->getModule(), 'spamfilter', false);
        $this->setSetting($this->getModule(), 'allow_feedback', false);
        $this->setSetting($this->getModule(), 'allow_own_question', false);
        $this->setSetting($this->getModule(), 'allow_multiple_categories', true);
        $this->setSetting($this->getModule(), 'send_email_on_new_feedback', false);

        $this->addDefaultCategories($faqId);
        $this->insertWidget();

        $this->setBackendNavigation();
    }

    private function setRights()
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'Index');
        $this->setActionRights(1, $this->getModule(), 'Add');
        $this->setActionRights(1, $this->getModule(), 'Edit');
        $this->setActionRights(1, $this->getModule(), 'Delete');
        $this->setActionRights(1, $this->getModule(), 'Sequence');
        $this->setActionRights(1, $this->getModule(), 'Categories');
        $this->setActionRights(1, $this->getModule(), 'AddCategory');
        $this->setActionRights(1, $this->getModule(), 'EditCategory');
        $this->setActionRights(1, $this->getModule(), 'DeleteCategory');
        $this->setActionRights(1, $this->getModule(), 'SequenceQuestions');
        $this->setActionRights(1, $this->getModule(), 'DeleteFeedback');
        $this->setActionRights(1, $this->getModule(), 'Settings');
    }

    private function addDefaultCategories($faqId)
    {
        foreach ($this->getLanguages() as $language) {
            $this->defaultCategory = $this->getCategory($language);

            // no category exists
            if (empty($this->defaultCategory)) {
                $this->defaultCategory = $this->addCategory($language, 'Default', 'default');
            }

            // check if a page for the faq already exists in this language
            $faqPageExists = (bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM pages AS p
                 INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
                 WHERE b.extra_id = ? AND p.language = ?
                 LIMIT 1',
                array($faqId, $language)
            );

            if (!$faqPageExists) {
                // insert page
                $this->insertPage(
                    array(
                        'title' => 'FAQ',
                        'language' => $language
                    ),
                    null,
                    array(
                         'extra_id' => $faqId)
                );
            }
        }
    }

    private function setBackendNavigation()
    {
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationFaqId = $this->setNavigation($navigationModulesId, $this->getModule());
        $this->setNavigation(
            $navigationFaqId,
            'Questions',
            'faq/index',
            array('faq/add', 'faq/edit')
        );
        $this->setNavigation(
            $navigationFaqId,
            'Categories',
            'faq/categories',
            array('faq/add_category', 'faq/edit_category')
        );
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, $this->getModule(), 'faq/settings');
    }
}
