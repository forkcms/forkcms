<?php

namespace App\Backend\Modules\Pages\Actions;

use App\Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use App\Backend\Core\Engine\Exception as BackendException;
use App\Backend\Core\Engine\Model as BackendModel;
use App\Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * BackendPagesCopy
 * This is the copy-action, it will copy pages from one language to another
 * Remark :    IMPORTANT existing data will be removed, this feature is also experimental!
 */
class Copy extends BackendBaseActionIndex
{
    /**
     * The languages
     *
     * @var string
     */
    private $from;
    private $to;

    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $this->from = $this->getRequest()->query->get('from', '');
        $this->to = $this->getRequest()->query->get('to', '');

        // validate
        if ($this->from === '') {
            throw new BackendException('Specify a from-parameter.');
        }
        if ($this->to === '') {
            throw new BackendException('Specify a to-parameter.');
        }

        // copy pages
        BackendPagesModel::copy($this->from, $this->to);

        // redirect
        $this->redirect(BackendModel::createUrlForAction('Index') . '&report=copy-added&var=' . rawurlencode($this->to));
    }
}
