<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception;

class MediaFolderNotFound extends \Exception
{
    public static function forEmptyId(): self
    {
        return new self('The id you have given is null');
    }

    public static function forId(int $id): self
    {
        return new self('Can\'t find a MediaFolder with id = "' . $id . '"".');
    }
}
