<?php

namespace Backend\Modules\Locale\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * This action will delete a translation
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Filter variables
     *
     * @var array
     */
    private $filter;

    /**
     * @var string
     */
    private $filterQuery;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendLocaleModel::exists($this->id) && BackendAuthentication::getUser()->isGod()) {
            parent::execute();

            // filter options
            $this->setFilter();

            // get data
            $this->record = (array) BackendLocaleModel::get($this->id);

            // delete item
            BackendLocaleModel::delete(array($this->id));

            // build redirect URL
            $redirectUrl = BackendModel::createURLForAction('Index') . '&report=deleted&var=' . rawurlencode($this->record['name'] . ' (' . mb_strtoupper($this->record['language']) . ')') . $this->filterQuery;

            // item was deleted, so redirect
            $this->redirect($redirectUrl);
        } else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        $this->filter['language'] = ($this->getParameter('language', 'array') != '') ? $this->getParameter('language', 'array') : BL::getWorkingLanguage();
        $this->filter['application'] = $this->getParameter('application');
        $this->filter['module'] = $this->getParameter('module');
        $this->filter['type'] = $this->getParameter('type', 'array');
        $this->filter['name'] = $this->getParameter('name');
        $this->filter['value'] = $this->getParameter('value');

        $this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
    }
}
