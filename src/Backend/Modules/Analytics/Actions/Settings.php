<?php

namespace Backend\Modules\Analytics\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Analytics\Engine\Helper as BackendAnalyticsHelper;
use Backend\Modules\Analytics\Engine\Model as BackendAnalyticsModel;

/**
 * This is the settings-action, it will display a form to set general analytics settings
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
 */
class Settings extends BackendBaseActionEdit
{
    /**
     * The information for the coupled web profiles
     *
     * @var    array
     */
    private $accountNames = array();
    private $profileTitles = array();
    private $tableIds = array();

    /**
     * API key needed by the API.
     *
     * @var string
     */
    private $apiKey;

    /**
     * The forms used on this page
     *
     * @var BackendForm
     */
    private $frmTrackingType;

    /**
     * All website profiles
     *
     * @var    array
     */
    private $profiles = array();


    /**
     * The session token
     *
     * @var    string
     */
    private $sessionToken;

    /**
     * The dropdowns containing the possible table ids
     *
     * @var array
     */
    private $dropdowns;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadTrackingTypeForm();
        $this->validateTrackingTypeForm();
        $this->getAnalyticsParameters();
        $this->parse();
        $this->display();
    }

    /**
     * Gets all the needed parameters to link a google analytics account to fork
     */
    private function getAnalyticsParameters()
    {
        $remove = \SpoonFilter::getGetValue('remove', array('session_token', 'table_id'), null);

        // something has to be removed before proceeding
        if (!empty($remove)) {
            $this->removeSetting($remove);
        }

        // get session token, account name, the profile's table id, the profile's title
        $this->sessionToken = BackendModel::getModuleSetting($this->getModule(), 'session_token', null);
        $this->accountNames = BackendModel::getModuleSetting($this->getModule(), 'account_names', array());
        $this->tableIds = BackendModel::getModuleSetting($this->getModule(), 'table_ids', array());
        $this->profileTitles = BackendModel::getModuleSetting($this->getModule(), 'profile_titles', array());
        $this->apiKey = BackendModel::getModuleSetting($this->getModule(), 'api_key', null);

        // no session token
        if (!isset($this->sessionToken)) {
            $this->getSessionTokenFromUrl();
        }

        // session id is present but there is no table_id
        if (isset($this->sessionToken) && empty($this->tableIds)) {
            $this->getAnalyticsProfiles();
        }
    }

    /**
     * Load settings form
     */
    private function loadTrackingTypeForm()
    {
        $this->frmTrackingType = new BackendForm('trackingType');

        $types = array();
        $types[] = array('label' => 'Universal Analytics', 'value' => 'universal_analytics');
        $types[] = array('label' => 'Classic Google Analytics', 'value' => 'classic_analytics');
        $types[] = array(
            'label' => 'Display Advertising (stats.g.doubleclick.net/dc.js)',
            'value' => 'display_advertising'
        );

        $this->frmTrackingType->addRadiobutton(
            'type',
            $types,
            BackendModel::getModuleSetting($this->URL->getModule(), 'tracking_type', 'universal_analytics')
        );
    }

    /**
     * Validates the tracking url form.
     */
    private function validateTrackingTypeForm()
    {
        if ($this->frmTrackingType->isSubmitted()) {
            if ($this->frmTrackingType->isCorrect()) {
                BackendModel::setModuleSetting(
                    $this->getModule(),
                    'tracking_type',
                    $this->frmTrackingType->getField('type')->getValue()
                );
                BackendModel::triggerEvent($this->getModule(), 'after_saved_tracking_type_settings');
                $this->redirect(BackendModel::createURLForAction('Settings') . '&report=saved');
            }
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        parent::parse();

        if (!isset($this->sessionToken)) {
            // show the link to the google account authentication form
            $this->tpl->assign('NoSessionToken', true);
            $this->tpl->assign('Wizard', true);

            // create form
            $this->handleApiKeyForm();
        } elseif (isset($this->sessionToken) && isset($this->profiles) && empty($this->tableIds)) {
            // session token is present but no table id
            $this->tpl->assign('NoTableId', true);
            $this->tpl->assign('Wizard', true);

            $accounts = array();

            if (!empty($this->profiles) && $this->profiles !== 'UNAUTHORIZED') {
                $accounts[''][0] = BL::msg('ChooseWebsiteProfile');

                // prepare accounts array
                foreach ((array) $this->profiles as $profile) {
                    $accounts[$profile['accountName']][$profile['tableId']] =
                        $profile['profileName'] . ' (' . $profile['webPropertyId'] . ')'
                    ;
                }

                if (!empty($accounts)) {
                    uksort($accounts, array(__CLASS__, 'sortAccounts'));

                    // create form
                    $this->handleProfileLinkForm($accounts);

                    // parse accounts
                    $this->tpl->assign('accounts', true);
                }
            }
        } elseif (isset($this->sessionToken) && !empty($this->tableIds) && isset($this->accountNames)) {
            // show the linked account
            $this->tpl->assign('EverythingIsPresent', true);

            // show the title of the linked account and profile
            $sites = $this->get('multisite')->getSites();
            foreach ($sites as $siteId => &$properties) {
                $properties = array(
                    'domain'       => $properties,
                    'accountName'  => $this->accountNames[$siteId],
                    'profileTitle' => $this->profileTitles[$siteId],
                );
            }
            $this->tpl->assign('properties', $sites);
        }

        // Parse tracking url form
        $this->frmTrackingType->parse($this->tpl);
    }

    protected function handleApiKeyForm()
    {
        // build the link to the google account authentication form
        $redirectUrl = SITE_URL . '/' . (strpos($this->URL->getQueryString(), '?') === false ?
            $this->URL->getQueryString() :
            substr($this->URL->getQueryString(), 0, strpos($this->URL->getQueryString(), '?')))
        ;
        $googleAccountAuthenticationForm = sprintf(
            BackendAnalyticsModel::GOOGLE_ACCOUNT_AUTHENTICATION_URL,
            urlencode($redirectUrl),
            urlencode(BackendAnalyticsModel::GOOGLE_ACCOUNT_AUTHENTICATION_SCOPE)
        );

        $frmApiKey = new BackendForm('apiKey');
        $frmApiKey->addText('key', $this->apiKey);

        if ($frmApiKey->isSubmitted()) {
            $frmApiKey->getField('key')->isFilled(BL::err('FieldIsRequired'));

            if ($frmApiKey->isCorrect()) {
                BackendModel::setModuleSetting(
                    $this->getModule(),
                    'api_key',
                    $frmApiKey->getField('key')->getValue()
                );
                $this->redirect($googleAccountAuthenticationForm);
            }
        }

        $frmApiKey->parse($this->tpl);
    }

    protected function handleProfileLinkForm($accounts)
    {
        $frmLinkProfile = new BackendForm(
            'linkProfile',
            BackendModel::createURLForAction(),
            'get'
        );

        $sites = $this->get('multisite')->getSites();

        // add a table_id dropdown for every site
        $this->dropdowns = array();
        foreach ($sites as $siteId => $domain) {
            $fieldName = 'table_id_' . $siteId;
            $frmLinkProfile->addDropdown($fieldName, $accounts);
            $this->dropdowns[] = array(
                'name'    => $fieldName,
                'site_id' => $siteId,
                'label'   => $domain,
                'field'   => $frmLinkProfile->getField($fieldName)->parse(),
            );
        }

        $this->tpl->assign('dropdowns', $this->dropdowns);
        $frmLinkProfile->parse($this->tpl);

        if ($frmLinkProfile->isSubmitted()) {
            $hasValue = false;
            foreach ($this->dropdowns as $dropdown) {
                if ($frmLinkProfile->getField($dropdown['name'])->getValue() != '0') {
                    $hasValue = true;
                    break;
                }
            }

            if (!$hasValue) {
                $this->tpl->assign('ddmTableIdError', BL::err('FieldIsRequired'));
            }
        }
    }

    /**
     * Helper function to sort accounts
     *
     * @param array $account1 First account for comparison.
     * @param array $account2 Second account for comparison.
     * @return int
     */
    public static function sortAccounts($account1, $account2)
    {
        if (strtolower($account1) > strtolower($account2)) {
            return 1;
        }
        if (strtolower($account1) < strtolower($account2)) {
            return -1;
        }

        return 0;
    }

    /**
     * Removes the needed module settings
     *
     * @param string $remove
     */
    protected function removeSetting($remove)
    {
        // the session token has te be removed
        if ($remove == 'session_token') {
            BackendModel::setModuleSetting($this->getModule(), 'session_token', null);
        }

        // remove all profile parameters from the module settings
        BackendModel::setModuleSetting($this->getModule(), 'account_names', array());
        BackendModel::setModuleSetting($this->getModule(), 'table_ids', array());
        BackendModel::setModuleSetting($this->getModule(), 'profile_titles', array());
        BackendModel::setModuleSetting($this->getModule(), 'web_property_ids', null);

        BackendAnalyticsModel::removeCacheFiles();
        BackendAnalyticsModel::clearTables();
    }

    /**
     * Fetches the session token from the url
     */
    protected function getSessionTokenFromUrl()
    {
        $token = \SpoonFilter::getGetValue('token', null, null);

        /*
         * Google's deprecated AuthSub authentication returns to this action with a
         * GET "token" param which causes a collision with our CSRF "token" param. This
         * is already resolved in an upcoming feature where we implement OAuth2.
         *
         * For now, and older releases, this is a work around. The token generated by Fork is
         * always 10 characters. The token provided by Google is larger then 10 characters, however
         * this is not documented and therefor cannot be trusted.
         */
        if (!empty($token) && strlen($token) > 10) {
            $ga = BackendAnalyticsHelper::getGoogleAnalyticsInstance();
            $this->sessionToken = $ga->getSessionToken($token);
            BackendModel::setModuleSetting($this->getModule(), 'session_token', $this->sessionToken);
        }
    }

    /**
     * Fetches Google Analytics profiles
     */
    protected function getAnalyticsProfiles()
    {
        $ga = BackendAnalyticsHelper::getGoogleAnalyticsInstance();

        try {
            $this->profiles = $ga->getAnalyticsAccountList($this->sessionToken);
        } catch (\GoogleAnalyticsException $e) {
            // bad request, probably means the API key is wrong
            if ($e->getCode() == '400') {
                // reset token so we can alter the API key
                BackendModel::setModuleSetting($this->getModule(), 'session_token', null);
                $this->redirect(
                    BackendModel::createURLForAction('Settings') . '&error=invalid-api-key'
                );
            }
        }

        if ($this->profiles == 'UNAUTHORIZED') {
            // remove invalid session token
            BackendModel::setModuleSetting($this->getModule(), 'session_token', null);

            // redirect to the settings page without parameters
            $this->redirect(BackendModel::createURLForAction('Settings'));
        } elseif (is_array($this->profiles)) {
            $sites = $this->get('multisite')->getSites();
            $webPropertyIds = array();
            foreach ($sites as $siteId => $domain) {
                $fieldName = 'table_id_' . $siteId;
                $tableId = \SpoonFilter::getGetValue($fieldName, null, null);

                // a table id is given in the get parameters
                if (!empty($tableId)) {
                    $profiles = array();

                    // set the table ids as keys
                    foreach ($this->profiles as $profile) {
                        $profiles[$profile['tableId']] = $profile;
                    }

                    // correct table id
                    if (isset($profiles[$tableId])) {
                        // save table id and account title
                        $this->tableIds[$siteId] = $tableId;
                        $this->accountNames[$siteId] = $profiles[$tableId]['profileName'];
                        $this->profileTitles[$siteId] = $profiles[$tableId]['title'];
                        $webPropertyIds[$siteId] = $profiles[$tableId]['webPropertyId'];
                    }
                }
            }

            // store the table id and account title in the settings
            BackendModel::setModuleSetting($this->getModule(), 'account_names', $this->accountNames);
            BackendModel::setModuleSetting($this->getModule(), 'table_ids', $this->tableIds);
            BackendModel::setModuleSetting($this->getModule(), 'profile_titles', $this->profileTitles);
            BackendModel::setModuleSetting($this->getModule(), 'web_property_ids', $webPropertyIds);
        }
    }
}
