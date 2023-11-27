<?php

namespace ForkCMS\Core\Domain\Header\Meta;

final class MetaCustom
{
    private function __construct(private readonly string $metaData)
    {
    }

    public function getUniqueKey(): string
    {
        return hash('xxh128', $this->metaData);
    }

    public function __toString(): string
    {
        return $this->metaData;
    }

    public static function fromString(string $metaData): self
    {
        return new self($metaData);
    }
}
