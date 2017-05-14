<?php

namespace ForkCMS\Bundle\InstallerBundle\DBAL;

use Doctrine\DBAL\Connection;

final class InstallerConnection extends Connection
{
    public function connect(): bool
    {
        return false;
    }
}
