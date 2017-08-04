<?php

namespace Backend\Modules\Error\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Language\Language as BL;
use Common\Exception\ExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the index-action (default), it will display an error depending on a given parameters
 */
class Index extends BackendBaseActionIndex
{
    /** @var int */
    private $statusCode;

    public function execute(): void
    {
        parent::execute();
        $this->parse();
        $this->display();
    }

    protected function parse(): void
    {
        parent::parse();

        // grab the error-type from the parameters
        $errorType = $this->getRequest()->query->get('type');

        // set correct headers
        switch ($errorType) {
            case 'module-not-allowed':
            case 'action-not-allowed':
                $this->statusCode = Response::HTTP_FORBIDDEN;
                break;

            case 'not-found':
                $this->statusCode = Response::HTTP_NOT_FOUND;
                break;
            default:
                $this->statusCode = Response::HTTP_BAD_REQUEST;
                break;
        }

        // querystring provided?
        if ($this->getRequest()->query->get('querystring', '') !== '') {
            // split into file and parameters
            $chunks = explode('?', $this->getRequest()->query->get('querystring'));

            // get extension
            $extension = pathinfo($chunks[0], PATHINFO_EXTENSION);

            // if the file has an extension it is a non-existing-file
            if ($extension !== '' && $extension !== $chunks[0]) {
                // give a nice error, so we can detect which file is missing
                throw new ExitException(
                    'File not found',
                    'Requested file (' . htmlspecialchars($this->getRequest()->query->get('querystring')) . ') not found.',
                    Response::HTTP_NOT_FOUND
                );
            }
        }

        // assign the correct message into the template
        $this->template->assign('message', BL::err(\SpoonFilter::toCamelCase(htmlspecialchars($errorType), '-')));
    }

    public function getContent(): Response
    {
        return new Response(
            $this->content,
            $this->statusCode
        );
    }
}
