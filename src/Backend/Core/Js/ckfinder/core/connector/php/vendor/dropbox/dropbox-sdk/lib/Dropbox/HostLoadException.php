<?php
namespace Dropbox;

/**
 * Thrown by the `Host::loadFromJson` method if something goes wrong.
 */
final class HostLoadException extends \Exception
{
    /**
     * @param string $message
     *
     * @internal
     */
    function __construct($message)
    {
        parent::__construct($message);
    }
}
