<?php

namespace Backend\Modules\Mailmotor\EventListener;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
    protected $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    public function onSettingsSavedEvent(SettingsSavedEvent $event)
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
