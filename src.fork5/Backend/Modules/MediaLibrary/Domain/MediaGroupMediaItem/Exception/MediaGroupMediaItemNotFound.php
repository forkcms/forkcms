<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\Exception;

class MediaGroupMediaItemNotFound extends \Exception
{
    public static function forMediaItemId(string $id): self
    {
        return new self("Can't find a MediaGroupMediaItem with mediaItemId = \"$id\".");
    }
}
