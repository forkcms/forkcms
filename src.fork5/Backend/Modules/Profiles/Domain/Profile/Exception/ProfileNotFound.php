<?php

namespace Backend\Modules\Profiles\Domain\Profile\Exception;

use Exception;

class ProfileNotFound extends Exception
{
    public static function forEmptyId(): self
    {
        return new self('The id you have given is null');
    }

    public static function forId(string $id): self
    {
        return new self('Can\'t find a Profile with id = "' . $id . '".');
    }
}
