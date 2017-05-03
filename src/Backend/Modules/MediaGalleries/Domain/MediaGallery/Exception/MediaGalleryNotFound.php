<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception;

use Exception;

class MediaGalleryNotFound extends Exception
{
    public static function forEmptyId(): self
    {
        return new self('The id you have given is null');
    }

    public static function forId(string $id): self
    {
        return new self('Can\'t find a MediaGallery with id = "' . $id . '".');
    }
}
