<?php

namespace Backend\Modules\MediaLibrary\ValueObject;

use Symfony\Component\Finder\Finder;

final class MediaWidget
{
    /** @var string */
    private $mediaWidget;

    /**
     * @param string $mediaWidget
     */
    private function __construct(string $mediaWidget)
    {
        if (!in_array($mediaWidget, self::getPossibleValues(), true)) {
            throw new \InvalidArgumentException('Invalid value');
        }

        $this->mediaWidget = $mediaWidget;
    }

    /**
     * @param string $mediaWidget
     * @return MediaWidget
     */
    public static function fromString(string $mediaWidget): MediaWidget
    {
        return new self($mediaWidget);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->mediaWidget;
    }

    /**
     * @param MediaWidget $mediaWidget
     * @return bool
     */
    public function equals(MediaWidget $mediaWidget): bool
    {
        if (!($mediaWidget instanceof $this)) {
            return false;
        }

        return $mediaWidget == $this;
    }

    /**
     * @return array
     */
    public static function getPossibleValues(): array
    {
        // Define actions
        $actions = array();

        $finder = new Finder();
        $finder->files()->in(
            FRONTEND_MODULES_PATH . '/MediaLibrary/Widgets'
        )->exclude('Base');

        foreach ($finder as $file) {
            $actions[] = $file->getBasename('.' . $file->getExtension());
        }

        return $actions;
    }

    /**
     * @return string
     */
    public function getMediaWidget(): string
    {
        return $this->mediaWidget;
    }
}
