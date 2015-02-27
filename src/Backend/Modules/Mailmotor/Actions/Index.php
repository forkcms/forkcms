<?php

namespace Backend\Modules\Mailmotor\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;

/**
 * This is the index-action (default), it will display the overview of mailings
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Index extends BackendBaseActionIndex
{
    // limits
    const QUEUED_MAILINGS_PAGING_LIMIT = 10;
    const SENT_MAILINGS_PAGING_LIMIT = 10;
    const UNSENT_MAILINGS_PAGING_LIMIT = 10;

    /**
     * The active campaign
     *
     * @var    array
     */
    private $campaign = array();

    /**
     * DataGrids
     *
     * @var    BackendDataGridDB
     */
    private $dgQueuedMailings;

    /**
     * DataGrids
     *
     * @var    BackendDataGridDB
     */
    private $dgSentMailings;

    /**
     * DataGrids
     *
     * @var    BackendDataGridDB
     */
    private $dgUnsentMailings;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // update the queued mailings with 'sent' status if their time has come already
        BackendMailmotorModel::updateQueuedMailings();

        // get the active campaign
        $this->getCampaign();

        // load datagrid
        $this->loadDataGrids();

        // parse page
        $this->parse();

        // display the page
        $this->display();
    }

    /**
     * Fetches the campaign ID and sets its record
     */
    private function getCampaign()
    {
        // get the active campaign
        $id = $this->getParameter('campaign', 'int');

        // fetch the campaign record
        $this->campaign = BackendMailmotorModel::getCampaign($id);
    }

    /**
     * Loads the datagrid with the unsent mailings
     */
    private function loadDataGridQueuedMailings()
    {
        // set query & parameters
        $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT;
        $parameters = array('queued');

        // campaign is set
        if (!empty($this->campaign)) {
            // reset query, add to parameters
            $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN;
            $parameters[] = $this->campaign['id'];
        }

        // create datagrid
        $this->dgQueuedMailings = new BackendDataGridDB($query, $parameters);
        $this->dgQueuedMailings->setColumnsHidden(array('campaign_id', 'status'));

        // if a campaign is set, hide the campaign name in the datagrid
        if (!empty($this->campaign)) {
            $this->dgQueuedMailings->setColumnHidden('campaign_name');
        }

        // set headers values
        $headers['sent'] = \SpoonFilter::ucfirst(BL::lbl('WillBeSentOn'));

        // set headers
        $this->dgQueuedMailings->setHeaderLabels($headers);

        // sorting columns
        $this->dgQueuedMailings->setSortingColumns(array('name', 'campaign_name', 'sent', 'language'), 'name');
        $this->dgQueuedMailings->setSortParameter('desc');

        // add the multicheckbox column
        $this->dgQueuedMailings->addColumn(
            'checkbox',
            '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>',
            '<span><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></span>'
        );
        $this->dgQueuedMailings->setColumnsSequence('checkbox');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
        $this->dgQueuedMailings->setMassAction($ddmMassAction);

        // set column functions
        $this->dgQueuedMailings->setColumnFunction(
            array(__CLASS__, 'setCampaignLink'),
            array('[campaign_id]', '[campaign_name]'),
            'campaign_name',
            true
        );
        $this->dgQueuedMailings->setColumnFunction('date', array('Y-m-d @ H:i', '[sent]'), 'sent', true);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Copy')) {
            $this->dgQueuedMailings->addColumnAction(
                'copy',
                null,
                BL::lbl('Copy'),
                BackendModel::createURLForAction('Copy') . '&amp;id=[id]',
                BL::lbl('Copy'),
                array('class' => 'button icon iconMailAdd linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditMailingCampaign')) {
            $this->dgQueuedMailings->addColumnAction(
                'edit_mailing_campaign',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditMailingCampaign') . '&amp;id=[id]',
                BL::lbl('EditMailingCampaign'),
                array('class' => 'button icon iconFolderEdit linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Statistics')) {
            $this->dgQueuedMailings->addColumnAction(
                'statistics',
                null,
                BL::lbl('Statistics'),
                BackendModel::createURLForAction('Statistics') . '&amp;id=[id]',
                BL::lbl('Statistics'),
                array('class' => 'button icon iconStats linkButton')
            );
        }

        // add styles
        $this->dgQueuedMailings->setColumnAttributes('name', array('class' => 'title'));

        // set paging limit
        $this->dgQueuedMailings->setPagingLimit(static::SENT_MAILINGS_PAGING_LIMIT);
    }

    /**
     * Loads the datagrids for mailings
     */
    private function loadDataGrids()
    {
        // load sent mailings
        $this->loadDataGridQueuedMailings();

        // load unsent mailings
        $this->loadDataGridUnsentMailings();

        // load sent mailings
        $this->loadDataGridSentMailings();
    }

    /**
     * Loads the datagrid with the unsent mailings
     */
    private function loadDataGridSentMailings()
    {
        // set query & parameters
        $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT;
        $parameters = array('sent');

        // campaign is set
        if (!empty($this->campaign)) {
            // reset query, add to parameters
            $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_SENT_FOR_CAMPAIGN;
            $parameters[] = $this->campaign['id'];
        }

        // create datagrid
        $this->dgSentMailings = new BackendDataGridDB($query, $parameters);
        $this->dgSentMailings->setColumnsHidden(array('campaign_id', 'status'));

        // if a campaign is set, hide the campaign name in the datagrid
        if (!empty($this->campaign)) {
            $this->dgSentMailings->setColumnHidden('campaign_name');
        }

        // sorting columns
        $this->dgSentMailings->setSortingColumns(array('name', 'campaign_name', 'sent', 'language'), 'sent');
        $this->dgSentMailings->setSortParameter('desc');

        // add the multicheckbox column
        $this->dgSentMailings->addColumn(
            'checkbox',
            '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>',
            '<span><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></span>'
        );
        $this->dgSentMailings->setColumnsSequence('checkbox');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
        $this->dgSentMailings->setMassAction($ddmMassAction);

        // set column functions
        $this->dgSentMailings->setColumnFunction(
            array(__CLASS__, 'setCampaignLink'),
            array('[campaign_id]', '[campaign_name]'),
            'campaign_name',
            true
        );
        $this->dgSentMailings->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[sent]'),
            'sent',
            true
        );

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Copy')) {
            $this->dgSentMailings->addColumnAction(
                'copy',
                null,
                BL::lbl('Copy'),
                BackendModel::createURLForAction('Copy') . '&amp;id=[id]',
                BL::lbl('Copy'),
                array('class' => 'button icon iconMailAdd linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditMailingCampaign')) {
            $this->dgSentMailings->addColumnAction(
                'edit_mailing_campaign',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('EditMailingCampaign') . '&amp;id=[id]',
                BL::lbl('EditMailingCampaign'),
                array('class' => 'button icon iconFolderEdit linkButton')
            );
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Statistics')) {
            $this->dgSentMailings->addColumnAction(
                'statistics',
                null,
                BL::lbl('Statistics'),
                BackendModel::createURLForAction('Statistics') . '&amp;id=[id]',
                BL::lbl('Statistics'),
                array('class' => 'button icon iconStats linkButton')
            );
        }

        // add styles
        $this->dgUnsentMailings->setColumnAttributes('name', array('class' => 'title'));

        // set paging limit
        $this->dgSentMailings->setPagingLimit(static::SENT_MAILINGS_PAGING_LIMIT);
    }

    /**
     * Loads the datagrid with the unsent mailings
     */
    private function loadDataGridUnsentMailings()
    {
        // set query & parameters
        $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_UNSENT;
        $parameters = array('concept');

        // campaign is set
        if (!empty($this->campaign)) {
            // reset query, add to parameters
            $query = BackendMailmotorModel::QRY_DATAGRID_BROWSE_UNSENT_FOR_CAMPAIGN;
            $parameters[] = $this->campaign['id'];
        }

        // create datagrid
        $this->dgUnsentMailings = new BackendDataGridDB($query, $parameters);
        $this->dgUnsentMailings->setColumnsHidden(array('campaign_id', 'status'));

        // if a campaign is set, hide the campaign name in the datagrid
        if (!empty($this->campaign)) {
            $this->dgUnsentMailings->setColumnHidden('campaign_name');
        }

        // sorting columns
        $this->dgUnsentMailings->setSortingColumns(
            array('name', 'campaign_name', 'created_on', 'language'),
            'created_on'
        );
        $this->dgUnsentMailings->setSortParameter('desc');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // set colum URLs
            $this->dgUnsentMailings->setColumnURL('name', BackendModel::createURLForAction('Edit') . '&amp;id=[id]');

            // add edit column
            $this->dgUnsentMailings->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }

        // add the multicheckbox column
        $this->dgUnsentMailings->addColumn(
            'checkbox',
            '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>',
            '<span><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></span>'
        );
        $this->dgUnsentMailings->setColumnsSequence('checkbox');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
        $this->dgUnsentMailings->setMassAction($ddmMassAction);

        // set column functions
        $this->dgUnsentMailings->setColumnFunction(
            array(__CLASS__, 'setCampaignLink'),
            array('[campaign_id]', '[campaign_name]'),
            'campaign_name',
            true
        );
        $this->dgUnsentMailings->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getTimeAgo'),
            array('[created_on]'),
            'created_on',
            true
        );

        // add styles
        $this->dgUnsentMailings->setColumnAttributes('name', array('class' => 'title'));

        // set paging limit
        $this->dgUnsentMailings->setPagingLimit(static::UNSENT_MAILINGS_PAGING_LIMIT);
    }

    /**
     * Parse all datagrids
     */
    protected function parse()
    {
        parent::parse();

        // a campaign was found, so parse the campaign record
        if (!empty($this->campaign)) {
            $this->tpl->assign($this->campaign);
        }

        // parse the datagrid for all unsent mailings
        $this->tpl->assign(
            'dgUnsentMailings',
            (string) $this->dgUnsentMailings->getContent()
        );

        // parse the datagrid for all sent mailings
        $this->tpl->assign(
            'dgSentMailings',
            (string) $this->dgSentMailings->getContent()
        );

        // parse the datagrid for all queued mailings
        $this->tpl->assign(
            'dgQueuedMailings',
            (string) $this->dgQueuedMailings->getContent()
        );
    }

    /**
     * Sets the correct campaign link in the datagrid
     *
     * @param int    $id   The ID of the campaign.
     * @param string $name The name of the campaign.
     * @return string
     */
    public static function setCampaignLink($id, $name)
    {
        return !empty($name) ? '<a href="' . SITE_URL . BackendModel::createURLForAction(
                'Index'
            ) . '&amp;campaign=' . $id . '">' . $name . '</a>' : \SpoonFilter::ucfirst(BL::lbl('NoCampaign'));
    }
}
