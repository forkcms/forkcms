<?php

namespace Backend\Modules\Blog\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the index-action (default), it will display the overview of blog posts
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The category where is filtered on
     *
     * @var array
     */
    private $category;

    /**
     * The id of the category where is filtered on
     *
     * @var int
     */
    private $categoryId;

    /**
     * DataGrids
     *
     * @var BackendDataGridDatabase
     */
    private $dgDrafts;
    private $dgPosts;
    private $dgRecent;

    public function execute(): void
    {
        parent::execute();

        // set category id
        $this->categoryId = $this->getRequest()->query->getInt('category');
        if ($this->categoryId === 0) {
            $this->categoryId = null;
        } else {
            // get category
            $this->category = BackendBlogModel::getCategory($this->categoryId);

            // reset
            if (empty($this->category)) {
                // reset GET to trick Spoon
                $_GET['category'] = null;

                // reset
                $this->categoryId = null;
            }
        }

        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    private function loadDataGridAllPosts(): void
    {
        // filter on category?
        if ($this->categoryId != null) {
            // create datagrid
            $this->dgPosts = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE_FOR_CATEGORY,
                [$this->categoryId, 'active', BL::getWorkingLanguage()]
            );

            // set the URL
            $this->dgPosts->setURL('&amp;category=' . $this->categoryId, true);
        } else {
            // create datagrid
            $this->dgPosts = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE,
                ['active', BL::getWorkingLanguage()]
            );
        }

        $this->dgPosts->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);

        // set headers
        $this->dgPosts->setHeaderLabels(
            [
                'user_id' => \SpoonFilter::ucfirst(BL::lbl('Author')),
                'publish_on' => \SpoonFilter::ucfirst(BL::lbl('PublishedOn')),
            ]
        );

        // hide columns
        $this->dgPosts->setColumnsHidden(['revision_id']);

        // sorting columns
        $this->dgPosts->setSortingColumns(['publish_on', 'title', 'user_id', 'comments'], 'publish_on');
        $this->dgPosts->setSortParameter('desc');

        // set column functions
        $this->dgPosts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[publish_on]'],
            'publish_on',
            true
        );
        $this->dgPosts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id',
            true
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgPosts->setRowAttributes(['id' => 'row-[revision_id]']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgPosts->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId
            );

            // add edit column
            $this->dgPosts->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId,
                BL::lbl('Edit')
            );
        }
    }

    private function loadDataGridDrafts(): void
    {
        // filter on category?
        if ($this->categoryId != null) {
            // create datagrid
            $this->dgDrafts = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE_DRAFTS_FOR_CATEGORY,
                [
                    $this->categoryId,
                    'draft',
                    BackendAuthentication::getUser()->getUserId(),
                    BL::getWorkingLanguage(),
                ]
            );

            // set the URL
            $this->dgDrafts->setURL('&amp;category=' . $this->categoryId, true);
        } else {
            // create datagrid
            $this->dgDrafts = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE_DRAFTS,
                ['draft', BackendAuthentication::getUser()->getUserId(), BL::getWorkingLanguage()]
            );
        }

        $this->dgDrafts->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);

        // set headers
        $this->dgDrafts->setHeaderLabels(['user_id' => \SpoonFilter::ucfirst(BL::lbl('Author'))]);

        // hide columns
        $this->dgDrafts->setColumnsHidden(['revision_id']);

        // sorting columns
        $this->dgDrafts->setSortingColumns(['edited_on', 'title', 'user_id', 'comments'], 'edited_on');
        $this->dgDrafts->setSortParameter('desc');

        // set column functions
        $this->dgDrafts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[edited_on]'],
            'edited_on',
            true
        );
        $this->dgDrafts->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id',
            true
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgDrafts->setRowAttributes(['id' => 'row-[revision_id]']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set column URLs
            $this->dgDrafts->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;draft=[revision_id]&amp;category=' .
                $this->categoryId
            );

            // add edit column
            $this->dgDrafts->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;draft=[revision_id]&amp;category=' .
                $this->categoryId,
                BL::lbl('Edit')
            );
        }
    }

    private function loadDataGridRecentPosts(): void
    {
        // filter on category?
        if ($this->categoryId != null) {
            // create datagrid
            $this->dgRecent = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE_RECENT_FOR_CATEGORY,
                [$this->categoryId, 'active', BL::getWorkingLanguage(), 4]
            );

            // set the URL
            $this->dgRecent->setURL('&amp;category=' . $this->categoryId, true);
        } else {
            // create datagrid
            $this->dgRecent = new BackendDataGridDatabase(
                BackendBlogModel::QUERY_DATAGRID_BROWSE_RECENT,
                ['active', BL::getWorkingLanguage(), 4]
            );
        }
        $this->dgRecent->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);

        // set headers
        $this->dgRecent->setHeaderLabels(['user_id' => \SpoonFilter::ucfirst(BL::lbl('Author'))]);

        // hide columns
        $this->dgRecent->setColumnsHidden(['revision_id']);

        // set paging
        $this->dgRecent->setPaging(false);

        // set column functions
        $this->dgRecent->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[edited_on]'],
            'edited_on',
            true
        );
        $this->dgRecent->setColumnFunction(
            [new BackendDataGridFunctions(), 'getUser'],
            ['[user_id]'],
            'user_id',
            true
        );

        // our JS needs to know an id, so we can highlight it
        $this->dgRecent->setRowAttributes(['id' => 'row-[revision_id]']);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set colum URLs
            $this->dgRecent->setColumnURL(
                'title',
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId
            );

            // add edit column
            $this->dgRecent->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('Edit') .
                '&amp;id=[id]&amp;category=' . $this->categoryId,
                BL::lbl('Edit')
            );
        }
    }

    private function loadDataGrids(): void
    {
        $this->loadDataGridAllPosts();
        $this->loadDataGridDrafts();

        // the most recent blogposts, only shown when we have more than 1 page in total
        if ($this->dgPosts->getNumResults() > $this->dgPosts->getPagingLimit()) {
            $this->loadDataGridRecentPosts();
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // parse the datagrid for the drafts
        $this->template->assign('dgDrafts', (string) $this->dgDrafts->getContent());

        // parse the datagrid for all blogposts
        $this->template->assign('dgPosts', (string) $this->dgPosts->getContent());

        // parse the datagrid for the most recent blogposts
        $this->template->assign('dgRecent', (is_object($this->dgRecent)) ? $this->dgRecent->getContent() : false);

        // get categories
        $categories = BackendBlogModel::getCategories(true);

        $hasMultipleCategories = (count($categories) > 1);
        $this->template->assign('hasMultipleCategories', $hasMultipleCategories);

        // multiple categories?
        if ($hasMultipleCategories) {
            // create form
            $form = new BackendForm('filter', null, 'get', false);

            // create element
            $form->addDropdown('category', $categories, $this->categoryId);
            $form->getField('category')->setDefaultElement('');

            // parse the form
            $form->parse($this->template);
        }

        // parse category
        if (!empty($this->category)) {
            $this->template->assign('filterCategory', $this->category);
        }
    }
}
