<?php

namespace Common\Core\Header;

final class Asset
{
    /** @var string */
    private $file;

    /** @var Priority */
    private $priority;

    /** @var bool */
    private $addTimestamp;

    /** @var int */
    private $sequence;

    public function __construct(string $file, bool $addTimestamp = false, Priority $priority = null)
    {
        $this->file = $file;
        $this->addTimestamp = $addTimestamp;
        $this->priority = $priority ?? Priority::standard();
        $this->sequence = 0;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function forCacheUrl(string $cacheUrl): self
    {
        $cacheAsset = clone $this;
        $cacheAsset->file = $cacheUrl;

        return $cacheAsset;
    }

    public function setSequence(int $sequence): void
    {
        $this->sequence = $sequence;
    }

    public function __toString(): string
    {
        if (!$this->addTimestamp) {
            return $this->file;
        }

        // check if we need to use a ? or &
        return $this->file . (mb_strpos($this->file, '?') === false ? '?' : '&') . 'm=' . LAST_MODIFIED_TIME;
    }
}
