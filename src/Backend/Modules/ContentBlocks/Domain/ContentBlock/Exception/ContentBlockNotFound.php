<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Exception;

use Exception;

class ContentBlockNotFound extends Exception
{
    public static function forEmptyId(): self
    {
        return new self('The id you have given is null');
    }

    public static function forEmptyRevisionId(): self
    {
        return new self('The revision-id you have given is null');
    }

    public static function forId(string $id): self
    {
        return new self('Can\'t find a ContentBlock with id = "' . $id . '".');
    }

    public static function forRevisionId(string $id): self
    {
        return new self('Can\'t find a ContentBlock with revision-id = "' . $id . '".');
    }
}
