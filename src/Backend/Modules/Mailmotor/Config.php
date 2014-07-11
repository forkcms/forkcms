<?php

namespace Backend\Modules\Mailmotor;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailmotor\Engine\Model as BackendMailmotorModel;
use Backend\Modules\Mailmotor\Engine\CMHelper as BackendMailmotorCMHelper;

/**
 * This is the configuration-object for the mailmotor module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var    string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var    array
     */
    protected $disabledActions = array();

    /**
     * Check if all required settings have been set
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param string                                        $module The module.
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        parent::__construct($kernel, $module);

        $url = $this->getContainer()->has('url') ? $this->getContainer()->get('url') : null;

        // do the client ID check if we're not in the settings page
        if ($url != null &&
            !in_array(
                $url->getAction(),
                array('Settings', 'ImportGroups', 'LinkAccount', 'LoadClientInfo')
            )
        ) {
            $this->checkForAccount();
            $this->checkForClientID();
            $this->checkForGroups();
        }
    }

    /**
     * Checks if a general CM account is made or not
     */
    private function checkForAccount()
    {
        // if the settings were set and we can make a connection
        if ($this->checkForSettings()) {
            // no connection to campaignmonitor could be made, so the service is probably unreachable at this point
            if (!BackendMailmotorCMHelper::checkAccount()) {
                \SpoonHTTP::redirect(
                    BackendModel::createURLForAction(
                        'Index',
                        'Mailmotor',
                        BL::getWorkingLanguage()
                    ) . '&error=could-not-connect'
                );
            }
        } else {
            // no settings were set
            \SpoonHTTP::redirect(
                BackendModel::createURLForAction(
                    'Settings',
                    'Mailmotor',
                    BL::getWorkingLanguage()
                ) . '#tabSettingsAccount'
            );
        }
    }

    /**
     * Checks if a client ID was already set or not
     */
    private function checkForClientID()
    {
        // fetch client ID
        $clientId = BackendMailmotorCMHelper::getClientID();

        // no client ID set, so redirect to settings with an appropriate error message.
        if (empty($clientId)) {
            \SpoonHTTP::redirect(
                BackendModel::createURLForAction('Settings', 'Mailmotor', BL::getWorkingLanguage())
            );
        }

        // get price per email
        $pricePerEmail = BackendModel::getModuleSetting('Mailmotor', 'price_per_email');

        // check if a price per e-mail is set
        if (empty($pricePerEmail) && $pricePerEmail != 0) {
            \SpoonHTTP::redirect(
                BackendModel::createURLForAction(
                    'Settings',
                    'Mailmotor',
                    BL::getWorkingLanguage()
                ) . '&error=no-price-per-email'
            );
        }
    }

    /**
     * Checks for external groups, and parses a message to import them.
     *
     * @return mixed Returns false if the user already made groups.
     */
    private function checkForExternalGroups()
    {
        // get all CM groups
        $externalGroups = BackendMailmotorCMHelper::getCM()->getListsByClientId();

        // return the result
        return (!empty($externalGroups));
    }

    /**
     * Checks if any groups are made yet. Depending on the client that is linked to Fork, it will
     * create default groups if none were found in CampaignMonitor. If they were, the user is
     * presented with an overview to import all groups and their subscribers in Fork.
     */
    private function checkForGroups()
    {
        // groups are already set
        if (BackendModel::getModuleSetting('Mailmotor', 'cm_groups_set')) {
            return false;
        }

        // no CM data found
        if (!BackendMailmotorCMHelper::checkAccount()) {
            return false;
        }

        // check if there are external groups present in CampaignMonitor
        if ($this->checkForExternalGroups()) {
            // external groups were found, so redirect to the import_groups action
            \SpoonHTTP::redirect(BackendModel::createURLForAction('ImportGroups', 'Mailmotor'));
        }

        // fetch the default groups, language abbreviation is the array key
        $groups = BackendMailmotorModel::getDefaultGroups();

        // loop languages
        foreach (BL::getActiveLanguages() as $language) {
            // this language does not have a default group set
            if (!isset($groups[$language])) {
                // set group record
                $group['name'] = 'Website (' . strtoupper($language) . ')';
                $group['language'] = $language;
                $group['is_default'] = 'Y';
                $group['created_on'] = date('Y-m-d H:i:s');

                try {
                    // insert the group in CampaignMonitor
                    BackendMailmotorCMHelper::insertGroup($group);
                } catch (\CampaignMonitorException $e) {
                    // ignore
                }
            }
        }

        // we have groups set, and default groups chosen
        BackendModel::setModuleSetting('Mailmotor', 'cm_groups_set', true);
        BackendModel::setModuleSetting('Mailmotor', 'cm_groups_defaults_set', true);
    }

    /**
     * Checks if all necessary settings were set.
     */
    private function checkForSettings()
    {
        $url = BackendModel::getModuleSetting('Mailmotor', 'cm_url');
        $username = BackendModel::getModuleSetting('Mailmotor', 'cm_username');
        $password = BackendModel::getModuleSetting('Mailmotor', 'cm_password');
        $clientID = BackendModel::getModuleSetting('Mailmotor', 'cm_client_id');

        return (!empty($url) && !empty($username) && !empty($password) && !empty($clientID));
    }
}
