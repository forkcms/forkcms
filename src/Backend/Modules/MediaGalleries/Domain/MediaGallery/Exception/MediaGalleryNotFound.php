<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Exception;

class MediaGalleryNotFound extends \Exception
{
    public static function forEmptyId()
    {
        return new \Exception('The id you have given is null');
    }

    public static function forId($id)
    {
        return new \Exception('Can\'t find a MediaGallery with id = "' . $id . '".');
    }
}
