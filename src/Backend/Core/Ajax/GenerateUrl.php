<?php

namespace Backend\Core\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;

/**
 * This action will generate a valid url based upon the submitted url.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class GenerateUrl extends BackendBaseAJAXAction
{
    /**
     * @var BackendMeta
     */
    private $meta;

    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // create bogus form
        $frm = new BackendForm('meta');

        // get parameters
        $URL = \SpoonFilter::getPostValue('url', null, '', 'string');
        $metaId = \SpoonFilter::getPostValue('meta_id', null, null);
        $baseFieldName = \SpoonFilter::getPostValue('baseFieldName', null, '', 'string');
        $custom = \SpoonFilter::getPostValue('custom', null, false, 'bool');
        $className = \SpoonFilter::getPostValue('className', null, '', 'string');
        $methodName = \SpoonFilter::getPostValue('methodName', null, '', 'string');
        $parameters = \SpoonFilter::getPostValue('parameters', null, '', 'string');

        // cleanup values
        $metaId = $metaId ? (int) $metaId : null;
        $parameters = @unserialize($parameters);

        // meta object
        $this->meta = new BackendMeta($frm, $metaId, $baseFieldName, $custom);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback($className, $methodName, $parameters);

        // fetch generated meta url
        $URL = urldecode($this->meta->generateURL($URL));

        // output
        $this->output(self::OK, $URL);
    }
}
