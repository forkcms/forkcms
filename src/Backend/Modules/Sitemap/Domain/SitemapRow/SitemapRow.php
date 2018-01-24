<?php

namespace Backend\Modules\Sitemap\Domain\SitemapRow;

final class SitemapRow
{
    /** @var ChangeFrequency */
    private $changeFrequency;

    /** @var \DateTime */
    private $lastModifiedOn;

    /** @var string */
    private $url;

    /** @var int - Value between 0 and 10, will be divided by 10 */
    private $priority;

    public function __construct(
        string $url,
        \DateTime $lastModifiedOn,
        ChangeFrequency $changeFrequency,
        int $priority = 5
    ) {
        $this->url = $url;
        $this->lastModifiedOn = $lastModifiedOn;
        $this->changeFrequency = $changeFrequency;
        $this->setPriority($priority);
    }

    public function getChangeFrequency(): ChangeFrequency
    {
        return $this->changeFrequency;
    }

    public function getLastModifiedOn(): \DateTime
    {
        return $this->lastModifiedOn;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    private function setPriority(int $priority): void
    {
        if ($priority < 0 || $priority > 10) {
            throw new \Exception('Priority must be a value between 0 and 10');
        }

        $this->priority = $priority;
    }
}
