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
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\ServiceRuntime\Internal;
use WindowsAzure\Common\Internal\Utilities;

/**
 * The XML current state serializer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceRuntime\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class XmlCurrentStateSerializer
{
    /**
     * Serializes the current state.
     * 
     * @param CurrentState  $state        The current state.
     * @param IOutputStream $outputStream The output stream.
     * 
     * @return none
     */
    public function serialize($state, $outputStream)
    {
        $statusLeaseInfo = array(
            'StatusLease' => array(
                '@attributes' => array(
                    'ClientId' => $state->getClientId()
                )
            )
        );
        
        if ($state instanceof AcquireCurrentState) {
            $statusLeaseInfo['StatusLease']['Acquire'] = array(
                'Incarnation' => $state->getIncarnation(),
                'Status'      => $state->getStatus(),
                'Expiration'  => Utilities::isoDate(
                    date_timestamp_get($state->getExpiration())
                )
            );
        } else if ($state instanceof ReleaseCurrentState) {
            $statusLeaseInfo['StatusLease']['Release'] = array();
        }
        
        $currentState = Utilities::serialize($statusLeaseInfo, 'CurrentState');
        fwrite($outputStream, $currentState);
    }
}

