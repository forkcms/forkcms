<?php

namespace Backend\Modules\Error\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;

/**
 * This is the index-action (default), it will display an error depending on a given parameters
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->parse();
        $this->display();
    }

    /**
     * Parse the correct messages into the template
     */
    protected function parse()
    {
        parent::parse();

        // grab the error-type from the parameters
        $errorType = $this->getParameter('type');

        // set correct headers
        switch ($errorType) {
            case 'module-not-allowed':
            case 'action-not-allowed':
                header('HTTP/1.1 403 Forbidden');
                break;

            case 'not-found':
                header('HTTP/1.1 404 Not Found');
                break;
        }

        // querystring provided?
        if ($this->getParameter('querystring') !== null) {
            // split into file and parameters
            $chunks = explode('?', $this->getParameter('querystring'));

            // get extension
            $extension = pathinfo($chunks[0], PATHINFO_EXTENSION);

            // if the file has an extension it is a non-existing-file
            if ($extension != '' && $extension != $chunks[0]) {
                // set correct headers
                header('HTTP/1.1 404 Not Found');

                // give a nice error, so we can detect which file is missing
                echo 'Requested file (' . htmlspecialchars($this->getParameter('querystring')) . ') not found.';

                // stop script execution
                exit;
            }
        }

        // assign the correct message into the template
        $this->tpl->assign('message', BL::err(\SpoonFilter::toCamelCase(htmlspecialchars($errorType), '-')));
    }
}
