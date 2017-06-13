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

    public function execute(): void
    {
        $this->id = $this->getRequest()->query->getInt('id');

        // does the item exist
        if ($this->id !== 0 && BackendLocaleModel::exists($this->id) && BackendAuthentication::getUser()->isGod()) {
            parent::execute();

            // filter options
            $this->setFilter();

            // get data
            $this->record = (array) BackendLocaleModel::get($this->id);

            // delete item
            BackendLocaleModel::delete([$this->id]);

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
    private function setFilter(): void
    {
        $this->filter['language'] = $this->getRequest()->query->get('language', []);
        if (empty($this->filter['language'])) {
            $this->filter['language'] = BL::getWorkingLanguage();
        }
        $this->filter['application'] = $this->getRequest()->query->get('application');
        $this->filter['module'] = $this->getRequest()->query->get('module');
        $this->filter['type'] = $this->getRequest()->query->get('type', '');
        if ($this->filter['type'] === '') {
            $this->filter['type'] = null;
        }
        $this->filter['name'] = $this->getRequest()->query->get('name');
        $this->filter['value'] = $this->getRequest()->query->get('value');

        $this->filterQuery = BackendLocaleModel::buildURLQueryByFilter($this->filter);
    }
}
