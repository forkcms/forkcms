<?php

namespace Backend\Core\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This action will generate a valid url based upon the submitted url.
 */
class GenerateUrl extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $url = \SpoonFilter::getPostValue('url', null, '', 'string');
        $className = \SpoonFilter::getPostValue('className', null, '', 'string');
        $methodName = \SpoonFilter::getPostValue('methodName', null, '', 'string');
        $parameters = \SpoonFilter::getPostValue('parameters', null, '', 'string');

        // cleanup values
        $parameters = @unserialize($parameters);

        // fetch generated meta url
        $url = urldecode($this->get('fork.repository.meta')->generateURL($url, $className, $methodName, $parameters));

        // output
        $this->output(self::OK, $url);
    }
}
