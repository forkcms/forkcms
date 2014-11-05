<?php

namespace Backend\Modules\Mailmotor\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridArray as BackendDataGridArray;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This is the classic fork mailmotor widget
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Statistics extends BackendBaseWidget
{
    const PAGING_LIMIT = 10;

    /**
     * Execute the widget
     */
    public function execute()
    {
        $this->header->addCSS('widgets.css', 'Mailmotor');
        $this->setColumn('right');
        $this->setPosition(1);
        $this->parse();
        $this->display();
    }

    /**
     * Load the datagrid for statistics
     */
    private function loadStatistics()
    {
        // fetch the latest mailing
        $mailing = BackendMailmotorModel::getSentMailings(1);

        // check if a mailing was found
        if (empty($mailing)) {
            return false;
        }

        // check if a mailing was set
        if (!isset($mailing[0])) {
            return false;
        }

        // show the sent mailings block
        $this->tpl->assign('oSentMailings', true);

        // fetch the statistics for this mailing
        $stats = BackendMailmotorCMHelper::getStatistics($mailing[0]['id'], true);

        // reformat the send date
        $mailing[0]['sent'] = \SpoonDate::getDate('d-m-Y', $mailing[0]['sent']) . ' ' .
                              BL::lbl('At') . ' ' . \SpoonDate::getDate('H:i', $mailing);

        // get results
        $results[] = array('label' => BL::lbl('MailmotorLatestMailing'), 'value' => $mailing[0]['name']);
        $results[] = array('label' => BL::lbl('MailmotorSendDate'), 'value' => $mailing[0]['sent']);
        $results[] = array(
            'label' => BL::lbl('MailmotorSent'),
            'value' => $stats['recipients'] . ' (' . $stats['recipients_percentage'] . ')'
        );
        $results[] = array(
            'label' => BL::lbl('MailmotorOpened'),
            'value' => $stats['unique_opens'] . ' (' . $stats['unique_opens_percentage'] . ')'
        );
        $results[] = array(
            'label' => BL::lbl('MailmotorClicks'),
            'value' => $stats['clicks_total'] . ' (' . $stats['clicks_percentage'] . ')'
        );

        // there are some results
        if (!empty($results)) {
            // get the datagrid
            $dataGrid = new BackendDataGridArray($results);

            // no pagination
            $dataGrid->setPaging(false);

            // parse the datagrid
            $this->tpl->assign('dgMailmotorStatistics', $dataGrid->getContent());
        }
    }

    /**
     * Load the datagrid for subscriptions
     */
    private function loadSubscriptions()
    {
        // get results
        $results = BackendMailmotorModel::getRecentSubscriptions(self::PAGING_LIMIT);

        // there are some results
        if (!empty($results)) {
            // get the datagrid
            $dataGrid = new BackendDataGridArray($results);

            // no pagination
            $dataGrid->setPaging(false);

            // set column functions
            $dataGrid->setColumnFunction(
                array(new BackendDataGridFunctions(), 'getTimeAgo'),
                array('[subscribed_on]'),
                'subscribed_on',
                true
            );

            // check if this action is allowed
            if (BackendAuthentication::isAllowedAction('EditAddress', 'Mailmotor')) {
                // set edit link
                $dataGrid->setColumnURL(
                    'email',
                    BackendModel::createURLForAction('EditAddress', 'Mailmotor') . '&amp;email=[email]'
                );
            }

            // parse the datagrid
            $this->tpl->assign('dgMailmotorSubscriptions', $dataGrid->getContent());
        }
    }

    /**
     * Load the datagrid for unsubscriptions
     */
    private function loadUnsubscriptions()
    {
        // get results
        $results = BackendMailmotorModel::getRecentUnsubscriptions(self::PAGING_LIMIT);

        // there are some results
        if (!empty($results)) {
            $dataGrid = new BackendDataGridArray($results);
            $dataGrid->setPaging(false);
            $dataGrid->setColumnFunction(
                array(new BackendDataGridFunctions(), 'getTimeAgo'),
                array('[unsubscribed_on]'),
                'unsubscribed_on',
                true
            );

            // check if this action is allowed
            if (BackendAuthentication::isAllowedAction('EditAddress')) {
                $dataGrid->setColumnURL(
                    'email',
                    BackendModel::createURLForAction('EditAddress', 'Mailmotor') . '&amp;email=[email]'
                );
            }

            // parse the datagrid
            $this->tpl->assign('dgMailmotorUnsubscriptions', $dataGrid->getContent());
        }
    }

    /**
     * Parse stuff into the template
     */
    private function parse()
    {
        $this->loadStatistics();
        $this->loadSubscriptions();
        $this->loadUnsubscriptions();
    }
}
