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
use Backend\Modules\Pages\Engine\Copier as PagesCopier;

/**
 * BackendPagesCopy
 * This is the copy-action, it will copy pages from one language to another
 * @remark :    IMPORTANT existing data will be removed, this feature is also experimental!
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Sam Tubbax <sam@sumocoders.be>
 * @author Jeroen Desloovere <jeroen@siesqo.be>
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

    /**
     * The sites
     *
     * @var int
     */
    private $fromSite;
    private $toSite;

    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        $this->getParameters();

        $copier = new PagesCopier(BackendModel::get('database'));
        $copier->copy($this->from, $this->to, $this->fromSite, $this->toSite);

        // redirect
        $this->redirect(BackendModel::createURLForAction('Index') . '&report=copy-added&var=' . urlencode($this->to));
    }

    protected function getParameters()
    {
        $this->from = $this->getParameter('from');
        $this->to = $this->getParameter('to');
        $this->fromSite = $this->getParameter('from_site');
        $this->toSite = $this->getParameter('to_site');

        // validate required parameters
        if ($this->from == '') {
            throw new BackendException('Specify a from-parameter.');
        }
        if ($this->to == '') {
            throw new BackendException('Specify a to-parameter.');
        }

        // if no site_id's are specified, let's copy in the current site.
        if (empty($this->fromSite)) {
            $this->fromSite = $this->get('current_site')->getId();
        }
        if (empty($this->toSite)) {
            $this->toSite = $this->get('current_site')->getId();
        }
    }
}
