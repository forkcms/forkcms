<?php

namespace Api\V1;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the API.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Init extends \Common\Core\Init
{
    /**
     * @inheritdoc
     */
    protected $allowedTypes = array('Api');

    /**
     * @param string $type The type of init to load, possible values: Backend, BackendAjax, BackendCronjob
     */
    public function initialize($type)
    {
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $this->allowedTypes)) {
            exit('Invalid init-type');
        }

        // set type
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        $this->setDebugging();

        \SpoonFilter::disableMagicQuotes();
        $this->initSession();
    }

    /**
     * Start session
     */
    private function initSession()
    {
        \SpoonSession::start();
    }
}
