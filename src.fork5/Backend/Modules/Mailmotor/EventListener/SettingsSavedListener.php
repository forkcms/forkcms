<?php

namespace Backend\Modules\Mailmotor\EventListener;

use Backend\Modules\Mailmotor\Domain\Settings\Event\SettingsSavedEvent;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Settings saved listener
 */
final class SettingsSavedListener
{
    /**
     * @var string
     */
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function onSettingsSavedEvent(): void
    {
        /**
         * We must remove our container cache after this request.
         * Because this is not only saved in the module settings,
         * but the compiler pass pushes this in the container.
         * The settings cache is cleared, but the container should be cleared too,
         * to make it rebuild with the new chosen engine
         */
        $fs = new Filesystem();
        $fs->remove($this->cacheDirectory);
    }
}
