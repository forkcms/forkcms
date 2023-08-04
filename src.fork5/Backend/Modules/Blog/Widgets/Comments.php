<?php

namespace Backend\Modules\Blog\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Blog\Engine\Model as BackendBlogModel;

/**
 * This widget will show the latest comments
 */
class Comments extends BackendBaseWidget
{
    /**
     * The comments
     *
     * @var array
     */
    private $comments;

    /**
     * An array that contains the number of comments / status
     *
     * @var array
     */
    private $numCommentStatus;

    public function execute(): void
    {
        $this->setColumn('middle');
        $this->setPosition(0);
        $this->loadData();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        $this->comments = BackendBlogModel::getLatestComments('published', 5);
        $this->numCommentStatus = BackendBlogModel::getCommentStatusCount();
    }

    private function parse(): void
    {
        $this->template->assign('blogComments', $this->comments);

        // comments to moderate
        if (isset($this->numCommentStatus['moderation']) && (int) $this->numCommentStatus['moderation'] > 0) {
            $this->template->assign('blogNumCommentsToModerate', $this->numCommentStatus['moderation']);
        }
    }
}
