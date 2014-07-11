<?php

namespace Backend\Modules\Mailmotor\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the mailmotor module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Insert an empty admin dashboard sequence
     */
    private function insertWidget()
    {
        // build widget
        $statistics = array(
            'column' => 'right',
            'position' => 2,
            'hidden' => false,
            'present' => true
        );

        // insert widget
        $this->insertDashboardWidget('Mailmotor', 'Statistics', $statistics);
    }

    /**
     * Install the module
     */
    public function install()
    {
        // install settings
        $this->installSettings();

        // install the DB
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // install the mailmotor module
        $this->installModule();

        // install the pages for the module
        $this->installPages();

        // insert dashboard widget
        $this->insertWidget();

        // set navigation
        $navigationMailmotorId = $this->setNavigation(null, 'Mailmotor', null, null, 5);
        $this->setNavigation(
            $navigationMailmotorId,
            'Newsletters',
            'mailmotor/index',
            array(
                 'mailmotor/add',
                 'mailmotor/edit',
                 'mailmotor/edit_mailing_campaign',
                 'mailmotor/statistics',
                 'mailmotor/statistics_link',
                 'mailmotor/statistics_bounces',
                 'mailmotor/statistics_campaign',
                 'mailmotor/statistics_opens'
            )
        );
        $this->setNavigation(
            $navigationMailmotorId,
            'Campaigns',
            'mailmotor/campaigns',
            array(
                 'mailmotor/add_campaign',
                 'mailmotor/edit_campaign',
                 'mailmotor/statistics_campaigns'
            )
        );
        $this->setNavigation(
            $navigationMailmotorId,
            'MailmotorGroups',
            'mailmotor/groups',
            array(
                 'mailmotor/add_group',
                 'mailmotor/edit_group',
                 'mailmotor/custom_fields',
                 'mailmotor/add_custom_field',
                 'mailmotor/import_groups'
            )
        );
        $this->setNavigation(
            $navigationMailmotorId,
            'Addresses',
            'mailmotor/addresses',
            array(
                 'mailmotor/add_address',
                 'mailmotor/edit_address',
                 'mailmotor/import_addresses'
            )
        );

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Mailmotor', 'mailmotor/settings');
    }

    /**
     * Install the module and it's actions
     */
    private function installModule()
    {
        // module rights
        $this->setModuleRights(1, 'Mailmotor');

        // action rights
        $this->setActionRights(1, 'Mailmotor', 'Add');
        $this->setActionRights(1, 'Mailmotor', 'AddAddress');
        $this->setActionRights(1, 'Mailmotor', 'AddCampaign');
        $this->setActionRights(1, 'Mailmotor', 'AddCustomField');
        $this->setActionRights(1, 'Mailmotor', 'AddGroup');
        $this->setActionRights(1, 'Mailmotor', 'Addresses');
        $this->setActionRights(1, 'Mailmotor', 'Campaigns');
        $this->setActionRights(1, 'Mailmotor', 'Copy');
        $this->setActionRights(1, 'Mailmotor', 'CustomFields');
        $this->setActionRights(1, 'Mailmotor', 'DeleteBounces');
        $this->setActionRights(1, 'Mailmotor', 'DeleteCustomField');
        $this->setActionRights(1, 'Mailmotor', 'Edit');
        $this->setActionRights(1, 'Mailmotor', 'EditAddress');
        $this->setActionRights(1, 'Mailmotor', 'EditCampaign');
        $this->setActionRights(1, 'Mailmotor', 'EditCustomField');
        $this->setActionRights(1, 'Mailmotor', 'EditGroup');
        $this->setActionRights(1, 'Mailmotor', 'EditMailingCampaign');
        $this->setActionRights(1, 'Mailmotor', 'EditMailingIframe');
        $this->setActionRights(1, 'Mailmotor', 'ExportAddresses');
        $this->setActionRights(1, 'Mailmotor', 'ExportStatistics');
        $this->setActionRights(1, 'Mailmotor', 'ExportStatisticsCampaign');
        $this->setActionRights(1, 'Mailmotor', 'Groups');
        $this->setActionRights(1, 'Mailmotor', 'ImportAddresses');
        $this->setActionRights(1, 'Mailmotor', 'ImportGroups');
        $this->setActionRights(1, 'Mailmotor', 'Index');
        $this->setActionRights(1, 'Mailmotor', 'LinkAccount');
        $this->setActionRights(1, 'Mailmotor', 'LoadClientInfo');
        $this->setActionRights(1, 'Mailmotor', 'MassAddressAction');
        $this->setActionRights(1, 'Mailmotor', 'MassCampaignAction');
        $this->setActionRights(1, 'Mailmotor', 'MassCustomFieldAction');
        $this->setActionRights(1, 'Mailmotor', 'MassGroupAction');
        $this->setActionRights(1, 'Mailmotor', 'MassMailingAction');
        $this->setActionRights(1, 'Mailmotor', 'SaveContent');
        $this->setActionRights(1, 'Mailmotor', 'SaveSendDate');
        $this->setActionRights(1, 'Mailmotor', 'SendMailing');
        $this->setActionRights(1, 'Mailmotor', 'Settings');
        $this->setActionRights(1, 'Mailmotor', 'Statistics');
        $this->setActionRights(1, 'Mailmotor', 'StatisticsBounces');
        $this->setActionRights(1, 'Mailmotor', 'StatisticsCampaign');
        $this->setActionRights(1, 'Mailmotor', 'StatisticsLink');
    }

    /**
     * Install the pages for this module
     */
    private function installPages()
    {
        // add extra's
        $sentMailingsID = $this->insertExtra('Mailmotor', 'block', 'SentMailings', null, null, 'N', 3000);
        $subscribeFormID = $this->insertExtra('Mailmotor', 'block', 'SubscribeForm', 'Subscribe', null, 'N', 3001);
        $unsubscribeFormID = $this->insertExtra(
            'Mailmotor',
            'block',
            'UnsubscribeForm',
            'Unsubscribe',
            null,
            'N',
            3002
        );
        $widgetSubscribeFormID = $this->insertExtra(
            'Mailmotor',
            'widget',
            'SubscribeForm',
            'Subscribe',
            null,
            'N',
            3003
        );

        // get search extra id
        $searchId = (int) $this->getDB()->getVar(
            'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            array('Search', 'widget', 'Form')
        );

        // loop languages
        foreach ($this->getLanguages() as $language) {
            $parentID = $this->insertPage(
                array(
                     'title' => \SpoonFilter::ucfirst($this->getLocale('SentMailings', 'Core', $language, 'lbl', 'Frontend')),
                     'type' => 'root',
                     'language' => $language
                ),
                null,
                array('extra_id' => $sentMailingsID, 'position' => 'main'),
                array('extra_id' => $searchId, 'position' => 'top')
            );

            $this->insertPage(
                array(
                     'parent_id' => $parentID,
                     'title' => \SpoonFilter::ucfirst($this->getLocale('Subscribe', 'Core', $language, 'lbl', 'Frontend')),
                     'language' => $language
                ),
                null,
                array('extra_id' => $subscribeFormID, 'position' => 'main'),
                array('extra_id' => $searchId, 'position' => 'top')
            );

            $this->insertPage(
                array(
                     'parent_id' => $parentID,
                     'title' => \SpoonFilter::ucfirst($this->getLocale('Unsubscribe', 'Core', $language, 'lbl', 'Frontend')),
                     'language' => $language
                ),
                null,
                array('extra_id' => $unsubscribeFormID, 'position' => 'main'),
                array('extra_id' => $searchId, 'position' => 'top')
            );
        }
    }

    /**
     * Install settings
     */
    private function installSettings()
    {
        // add 'blog' as a module
        $this->addModule('Mailmotor');

        // get email from the session
        $email = \SpoonSession::exists('email') ? \SpoonSession::get('email') : null;

        // get from/replyTo Core settings
        $from = $this->getSetting('Core', 'mailer_from');
        $replyTo = $this->getSetting('Core', 'mailer_reply_to');

        // general settings
        $this->setSetting('Mailmotor', 'from_email', $from['email']);
        $this->setSetting('Mailmotor', 'from_name', $from['name']);
        $this->setSetting('Mailmotor', 'plain_text_editable', true);
        $this->setSetting('Mailmotor', 'reply_to_email', $replyTo['email']);
        $this->setSetting('Mailmotor', 'price_per_email', 0);
        $this->setSetting('Mailmotor', 'price_per_campaign', 0);

        // pre-load these CM settings -
        // these are used to obtain a client ID after the CampaignMonitor account is linked.
        $this->setSetting('Mailmotor', 'cm_url', '');
        $this->setSetting('Mailmotor', 'cm_username', '');
        $this->setSetting('Mailmotor', 'cm_password', '');
        $this->setSetting('Mailmotor', 'cm_client_company_name', $from['name']);
        $this->setSetting('Mailmotor', 'cm_client_contact_email', $from['email']);
        $this->setSetting('Mailmotor', 'cm_client_contact_name', $from['name']);
        $this->setSetting('Mailmotor', 'cm_client_country', 'Belgium');
        $this->setSetting('Mailmotor', 'cm_client_timezone', '');

        // by default no account is linked yet
        $this->setSetting('Mailmotor', 'cm_account', false);
    }
}
