<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception;

class MediaGroupNotFound extends \Exception
{
    public static function forEmptyId(): self
    {
        return new self('The id you have given is null');
    }

    public static function forId(string $id): self
    {
        return new self('Can\'t find a MediaGroup with id = "' . $id . '"".');
    }
}
