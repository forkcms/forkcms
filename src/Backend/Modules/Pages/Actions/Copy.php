<?php

namespace Backend\Modules\Pages\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

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
