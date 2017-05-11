<?php

namespace Backend\Modules\MediaLibrary\ValueObject;

use Symfony\Component\Finder\Finder;

final class MediaWidget
{
    /** @var string */
    private $mediaWidget;

    private function __construct(string $mediaWidget)
    {
        if (!in_array($mediaWidget, self::getPossibleValues(), true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaWidget = $mediaWidget;
    }

    public static function fromString(string $mediaWidget): MediaWidget
    {
        return new self($mediaWidget);
    }

    public function __toString(): string
    {
        return $this->mediaWidget;
    }

    public function equals(MediaWidget $mediaWidget): bool
    {
        return $mediaWidget->mediaWidget === $this->mediaWidget;
    }

    public static function getPossibleValues(): array
    {
        // Define actions
        $actions = [];

        $finder = new Finder();
        $finder->files()->in(FRONTEND_MODULES_PATH . '/MediaLibrary/Widgets')->exclude('Base');

        foreach ($finder as $file) {
            $actions[] = $file->getBasename('.' . $file->getExtension());
        }

        return $actions;
    }

    public function getMediaWidget(): string
    {
        return $this->mediaWidget;
    }
}
