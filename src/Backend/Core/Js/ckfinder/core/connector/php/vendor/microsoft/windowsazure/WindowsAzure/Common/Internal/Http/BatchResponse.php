<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Common\Internal\Http;
require_once 'PEAR.php';
require_once 'Mail/mimeDecode.php';
require_once 'HTTP/Request2/Response.php';
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\ServiceException;

/**
 * Batch response parser
 *
 * @category  Microsoft
 * @package   WindowsAzure\Common\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class BatchResponse
{
    /**
     * Http responses list
     *
     * @var array
     */
    private $_contexts;

    /**
     * Constructor
     *
     * @param string                                         $content Http response
     * as string
     *
     * @param WindowsAzure\Common\Internal\Http\BatchRequest $request Source batch
     * request object
     */
    public function __construct($content, $request = null)
    {
        $params['include_bodies'] = true;
        $params['input']          = $content;
        $mimeDecoder              = new \Mail_mimeDecode($content);
        $structure                = $mimeDecoder->decode($params);
        $parts                    = $structure->parts;
        $this->_contexts          = array();
        $requestContexts          = null;

        if ($request != null) {
            Validate::isA(
                $request,
                'WindowsAzure\Common\Internal\Http\BatchRequest',
                'request'
            );
            $requestContexts = $request->getContexts();
        }

        $i = 0;
        foreach ($parts as $part) {
            if (!empty($part->body)) {
                $headerEndPos = strpos($part->body, "\r\n\r\n");

                $header        = substr($part->body, 0, $headerEndPos);
                $body          = substr($part->body, $headerEndPos + 4);
                $headerStrings = explode("\r\n", $header);

                $response = new \HTTP_Request2_Response(array_shift($headerStrings));
                foreach ($headerStrings as $headerString) {
                    $response->parseHeaderLine($headerString);
                }
                $response->appendBody($body);

                $this->_contexts[] = $response;

                if (is_array($requestContexts)) {
                    $expectedCodes = $requestContexts[$i]->getStatusCodes();
                    $statusCode    = $response->getStatus();

                    if (!in_array($statusCode, $expectedCodes)) {
                        $reason = $response->getReasonPhrase();

                        throw new ServiceException($statusCode, $reason, $body);
                    }
                }

                $i++;
            }
        }
    }

    /**
     * Get parsed contexts as array
     *
     * @return array
     */
    public function getContexts()
    {
        return $this->_contexts;
    }

}

