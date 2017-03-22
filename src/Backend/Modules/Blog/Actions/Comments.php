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
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This is the comments-action , it will display the overview of blog comments
 */
class Comments extends BackendBaseActionIndex
{
    /**
     * DataGrids
     *
     * @var BackendDataGridDB
     */
    private $dgPublished;
    private $dgModeration;
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
    public static function addPostData($text, $title, $url, $id)
    {
        // reset URL
        $url = BackendModel::getURLForBlock('Blog', 'Detail') . '/' . $url . '#comment-' . $id;

        // build HTML
        return '<p><em>' . sprintf(BL::msg('CommentOnWithURL'), $url, $title) . '</em></p>' . "\n" . (string) $text;
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrids();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the datagrids
     */
    private function loadDataGrids()
    {
        /*
         * DataGrid for the published comments.
         */
        $this->dgPublished = new BackendDataGridDB(
            BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS,
            array('published', BL::getWorkingLanguage(), 'active')
        );

        // active tab
        $this->dgPublished->setActiveTab('tabPublished');

        // num items per page
        $this->dgPublished->setPagingLimit(30);

        // header labels
        $this->dgPublished->setHeaderLabels(array(
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ));

        // add the multicheckbox column
        $this->dgPublished->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgPublished->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            '[created_on]',
            'created_on',
            true
        );
        $this->dgPublished->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgPublished->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgPublished->setColumnFunction(
            array(new BackendDataGridFunctions(), 'cleanupPlaintext'),
            '[text]',
            'text',
            true
        );
        $this->dgPublished->setColumnFunction(
            array(__CLASS__, 'addPostData'),
            array('[text]', '[post_title]', '[post_url]', '[id]'),
            'text',
            true
        );

        // sorting
        $this->dgPublished->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
        $this->dgPublished->setSortParameter('desc');

        // hide columns
        $this->dgPublished->setColumnsHidden('post_id', 'post_title', 'post_url');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            array(
                'moderation' => BL::lbl('MoveToModeration'),
                'spam' => BL::lbl('MoveToSpam'),
                'delete' => BL::lbl('Delete'),
            ),
            'spam',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionPublished');
        $ddmMassAction->setOptionAttributes('delete', array('data-target' => '#confirmDeletePublished'));
        $ddmMassAction->setOptionAttributes('spam', array('data-target' => '#confirmPublishedToSpam'));
        $this->dgPublished->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditComment')) {
            $this->dgPublished->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditComment') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgPublished->addColumn(
                'mark_as_spam',
                null,
                BL::lbl('MarkAsSpam'),
                BackendModel::createURLForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=published&amp;action=spam',
                BL::lbl('MarkAsSpam')
            );
        }

        /*
         * DataGrid for the comments that are awaiting moderation.
         */

        // datagrid for the comments that are awaiting moderation
        $this->dgModeration = new BackendDataGridDB(
            BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS,
            array('moderation', BL::getWorkingLanguage(), 'active')
        );

        // active tab
        $this->dgModeration->setActiveTab('tabModeration');

        // num items per page
        $this->dgModeration->setPagingLimit(30);

        // header labels
        $this->dgModeration->setHeaderLabels(array(
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ));

        // add the multicheckbox column
        $this->dgModeration->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgModeration->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            '[created_on]',
            'created_on',
            true
        );
        $this->dgModeration->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgModeration->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgModeration->setColumnFunction(
            array(new BackendDataGridFunctions(), 'cleanupPlaintext'),
            '[text]',
            'text',
            true
        );
        $this->dgModeration->setColumnFunction(
            array(__CLASS__, 'addPostData'),
            array('[text]', '[post_title]', '[post_url]', '[id]'),
            'text',
            true
        );

        // sorting
        $this->dgModeration->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
        $this->dgModeration->setSortParameter('desc');

        // hide columns
        $this->dgModeration->setColumnsHidden('post_id', 'post_title', 'post_url');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            array(
                'published' => BL::lbl('MoveToPublished'),
                'spam' => BL::lbl('MoveToSpam'),
                'delete' => BL::lbl('Delete'),
            ),
            'published',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionModeration');
        $ddmMassAction->setOptionAttributes('delete', array('data-target' => '#confirmDeleteModeration'));
        $ddmMassAction->setOptionAttributes('spam', array('data-target' => '#confirmModerationToSpam'));
        $this->dgModeration->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditComment')) {
            $this->dgModeration->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditComment') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgModeration->addColumn(
                'approve',
                null,
                BL::lbl('Approve'),
                BackendModel::createURLForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=published&amp;action=published',
                BL::lbl('Approve')
            );
        }

        /*
         * DataGrid for the comments that are marked as spam
         */
        $this->dgSpam = new BackendDataGridDB(
            BackendBlogModel::QRY_DATAGRID_BROWSE_COMMENTS,
            array('spam', BL::getWorkingLanguage(), 'active')
        );

        // active tab
        $this->dgSpam->setActiveTab('tabSpam');

        // num items per page
        $this->dgSpam->setPagingLimit(30);

        // header labels
        $this->dgSpam->setHeaderLabels(array(
            'created_on' => \SpoonFilter::ucfirst(BL::lbl('Date')),
            'text' => \SpoonFilter::ucfirst(BL::lbl('Comment')),
        ));

        // add the multicheckbox column
        $this->dgSpam->setMassActionCheckboxes('check', '[id]');

        // assign column functions
        $this->dgSpam->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            '[created_on]',
            'created_on',
            true
        );
        $this->dgSpam->setColumnFunction('htmlspecialchars', ['[text]'], 'text');
        $this->dgSpam->setColumnFunction('htmlspecialchars', ['[author]'], 'author');
        $this->dgSpam->setColumnFunction(
            array(new BackendDataGridFunctions(), 'cleanupPlaintext'),
            '[text]',
            'text',
            true
        );
        $this->dgSpam->setColumnFunction(
            array(__CLASS__, 'addPostData'),
            array('[text]', '[post_title]', '[post_url]', '[id]'),
            'text',
            true
        );

        // sorting
        $this->dgSpam->setSortingColumns(array('created_on', 'text', 'author'), 'created_on');
        $this->dgSpam->setSortParameter('desc');

        // hide columns
        $this->dgSpam->setColumnsHidden('post_id', 'post_title', 'post_url');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown(
            'action',
            array(
                'published' => BL::lbl('MoveToPublished'),
                'moderation' => BL::lbl('MoveToModeration'),
                'delete' => BL::lbl('Delete'),
            ),
            'published',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMassAction->setAttribute('id', 'actionSpam');
        $ddmMassAction->setOptionAttributes('delete', array('data-target' => '#confirmDeleteSpam'));
        $this->dgSpam->setMassAction($ddmMassAction);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('MassCommentAction')) {
            $this->dgSpam->addColumn(
                'approve',
                null,
                BL::lbl('Approve'),
                BackendModel::createURLForAction('MassCommentAction') .
                '&amp;id=[id]&amp;from=spam&amp;action=published',
                BL::lbl('Approve')
            );
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        // published datagrid and num results
        $this->tpl->assign('dgPublished', (string) $this->dgPublished->getContent());
        $this->tpl->assign('numPublished', $this->dgPublished->getNumResults());

        // moderation datagrid and num results
        $this->tpl->assign('dgModeration', (string) $this->dgModeration->getContent());
        $this->tpl->assign('numModeration', $this->dgModeration->getNumResults());

        // spam datagrid and num results
        $this->tpl->assign('dgSpam', (string) $this->dgSpam->getContent());
        $this->tpl->assign('numSpam', $this->dgSpam->getNumResults());
    }
}
