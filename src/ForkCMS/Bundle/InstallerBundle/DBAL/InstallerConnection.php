<?php

namespace ForkCMS\Bundle\InstallerBundle\DBAL;

use Doctrine\DBAL\Connection;

final class InstallerConnection extends Connection
{
    /**
     * @return bool
     */
    public function connect()
    {
        return false;
    }
}
