<?php

namespace ForkCMS\Backend\Core\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * This action will generate a valid url based upon the submitted url.
 */
class GenerateUrl extends AjaxAction
{
    public function execute(): void
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $url = $this->getRequest()->request->get('url', '');
        $className = $this->getRequest()->request->get('className', '');
        $methodName = $this->getRequest()->request->get('methodName', '');
        $parameters = $this->getRequest()->request->get('parameters', '');

        // cleanup values
        $parameters = @unserialize($parameters);

        // fetch generated meta url
        $url = urldecode($this->get('fork.repository.meta')->generateUrl($url, $className, $methodName, $parameters));

        // output
        $this->output(Response::HTTP_OK, $url);
    }
}
