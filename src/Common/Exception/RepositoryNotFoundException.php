<?php

namespace Common\Exception;

use Exception;

final class RepositoryNotFoundException extends Exception
{
    public static function withRepository(string $repositoryFQCN): self
    {
        return new self("Could not find repository: '$repositoryFQCN'");
    }
}
