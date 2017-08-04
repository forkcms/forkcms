<?php

namespace Backend\Modules\Blog\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDatabase as BackendDataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the comments-action , it will display the overview of blog comments
 */
class Comments extends BackendBaseActionIndex
{
    /**
     * @var BackendDataGridDatabase
     */
    private $dgPublished;

    /**
     * @var BackendDataGridDatabase
     */
    private $dgModeration;

    /**
     * @var BackendDataGridDatabase
     */
    private $dgSpam;

    /**
     * Add postdata into the comment
     *
     * @param string $text The comment.
     * @param string $title The title for the blogarticle.
     * @param string $url The URL for the blogarticle.
     * @param int $id The id of the comment.
     *
     * @return string
     */
    public static function addPostData(string $text, string $title, string $url, int $id): string
    {
        // reset URL
        $url = BackendModel::getUrlForBlock('Blog', 'Detail') . '/' . $url . '#comment-' . $id;

        // build HTML
        return '<p><em>' . sprintf(BL::msg('CommentOnWithURL'), $url, $title) . '</em></p>' . "\n" . (string) $text;
    }

    public function execute(): void
    {
        parent::execute();
        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    private function loadDataGrids(): void
    {
        /*
         * DataGrid for the published comments.
         */
        $this->dgPublished = new BackendDataGridDatabase(
            BackendBlogModel::QUERY_DATAGRID_BROWSE_COMMENTS,
            ['published', BL::getWorkingLanguage(), 'active']
        );

        // active tab
        $this->dgPublished->setActiveTab('tabPublished');

        // num items per page
        $this->dgPublished->setPagingLimit(30);

        // header labels
        $this->dgPublished->setHeaderLabels([
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ]);

        // add the multicheckbox column
        $this->dgPublished->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgPublished->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            '[created_on]',
            'created_on',
            true
        );
        $this->dgPublished->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgPublished->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgPublished->setColumnFunction(
            [new BackendDataGridFunctions(), 'cleanupPlaintext'],
            '[text]',
            'text',
            true
        );
        $this->dgPublished->setColumnFunction(
            [__CLASS__, 'addPostData'],
            ['[text]', '[post_title]', '[post_url]', '[id]'],
            'text',
            true
        );

        // sorting
        $this->dgPublished->setSortingColumns(['created_on', 'text', 'author'], 'created_on');
        $this->dgPublished->setSortParameter('desc');

        // hide columns
        $this->dgPublished->setColumnsHidden(['post_id', 'post_title', 'post_url']);

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            [
                'moderation' => BL::lbl('MoveToModeration'),
                'spam' => BL::lbl('MoveToSpam'),
                'delete' => BL::lbl('Delete'),
            ],
            'spam',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionPublished');
        $ddmMassAction->setOptionAttributes('delete', ['data-target' => '#confirmDeletePublished']);
        $ddmMassAction->setOptionAttributes('spam', ['data-target' => '#confirmPublishedToSpam']);
        $this->dgPublished->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditComment')) {
            $this->dgPublished->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('EditComment') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgPublished->addColumn(
                'mark_as_spam',
                null,
                BL::lbl('MarkAsSpam'),
                BackendModel::createUrlForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=published&amp;action=spam',
                BL::lbl('MarkAsSpam')
            );
        }

        /*
         * DataGrid for the comments that are awaiting moderation.
         */

        // datagrid for the comments that are awaiting moderation
        $this->dgModeration = new BackendDataGridDatabase(
            BackendBlogModel::QUERY_DATAGRID_BROWSE_COMMENTS,
            ['moderation', BL::getWorkingLanguage(), 'active']
        );

        // active tab
        $this->dgModeration->setActiveTab('tabModeration');

        // num items per page
        $this->dgModeration->setPagingLimit(30);

        // header labels
        $this->dgModeration->setHeaderLabels([
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ]);

        // add the multicheckbox column
        $this->dgModeration->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgModeration->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            '[created_on]',
            'created_on',
            true
        );
        $this->dgModeration->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgModeration->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgModeration->setColumnFunction(
            [new BackendDataGridFunctions(), 'cleanupPlaintext'],
            '[text]',
            'text',
            true
        );
        $this->dgModeration->setColumnFunction(
            [__CLASS__, 'addPostData'],
            ['[text]', '[post_title]', '[post_url]', '[id]'],
            'text',
            true
        );

        // sorting
        $this->dgModeration->setSortingColumns(['created_on', 'text', 'author'], 'created_on');
        $this->dgModeration->setSortParameter('desc');

        // hide columns
        $this->dgModeration->setColumnsHidden(['post_id', 'post_title', 'post_url']);

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            [
                'published' => BL::lbl('MoveToPublished'),
                'spam' => BL::lbl('MoveToSpam'),
                'delete' => BL::lbl('Delete'),
            ],
            'published',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionModeration');
        $ddmMassAction->setOptionAttributes('delete', ['data-target' => '#confirmDeleteModeration']);
        $ddmMassAction->setOptionAttributes('spam', ['data-target' => '#confirmModerationToSpam']);
        $this->dgModeration->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditComment')) {
            $this->dgModeration->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createUrlForAction('EditComment') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgModeration->addColumn(
                'approve',
                null,
                BL::lbl('Approve'),
                BackendModel::createUrlForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=published&amp;action=published',
                BL::lbl('Approve')
            );
        }

        /*
         * DataGrid for the comments that are marked as spam
         */
        $this->dgSpam = new BackendDataGridDatabase(
            BackendBlogModel::QUERY_DATAGRID_BROWSE_COMMENTS,
            ['spam', BL::getWorkingLanguage(), 'active']
        );

        // active tab
        $this->dgSpam->setActiveTab('tabSpam');

        // num items per page
        $this->dgSpam->setPagingLimit(30);

        // header labels
        $this->dgSpam->setHeaderLabels([
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ]);

        // add the multicheckbox column
        $this->dgSpam->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgSpam->setColumnFunction(
            [new BackendDataGridFunctions(), 'getTimeAgo'],
            '[created_on]',
            'created_on',
            true
        );
        $this->dgSpam->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgSpam->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgSpam->setColumnFunction(
            [new BackendDataGridFunctions(), 'cleanupPlaintext'],
            '[text]',
            'text',
            true
        );
        $this->dgSpam->setColumnFunction(
            [__CLASS__, 'addPostData'],
            ['[text]', '[post_title]', '[post_url]', '[id]'],
            'text',
            true
        );

        // sorting
        $this->dgSpam->setSortingColumns(['created_on', 'text', 'author'], 'created_on');
        $this->dgSpam->setSortParameter('desc');

        // hide columns
        $this->dgSpam->setColumnsHidden(['post_id', 'post_title', 'post_url']);

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            [
                'published' => BL::lbl('MoveToPublished'),
                'moderation' => BL::lbl('MoveToModeration'),
                'delete' => BL::lbl('Delete'),
            ],
            'published',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionSpam');
        $ddmMassAction->setOptionAttributes('delete', ['data-target' => '#confirmDeleteSpam']);
        $this->dgSpam->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgSpam->addColumn(
                'approve',
                null,
                BL::lbl('Approve'),
                BackendModel::createUrlForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=spam&amp;action=published',
                BL::lbl('Approve')
            );
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // published datagrid and num results
        $this->template->assign('dgPublished', (string) $this->dgPublished->getContent());
        $this->template->assign('numPublished', $this->dgPublished->getNumResults());

        // moderation datagrid and num results
        $this->template->assign('dgModeration', (string) $this->dgModeration->getContent());
        $this->template->assign('numModeration', $this->dgModeration->getNumResults());

        // spam datagrid and num results
        $this->template->assign('dgSpam', (string) $this->dgSpam->getContent());
        $this->template->assign('numSpam', $this->dgSpam->getNumResults());
    }
}
