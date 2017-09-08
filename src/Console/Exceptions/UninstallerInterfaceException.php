<?php

namespace Console\Exceptions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Throwable;

/**
 * Uninstaller interface exception class
 */
class UninstallerInterfaceException extends \Exception
{
    /**
     * UninstallerInterfaceException constructor.
     * @param string $module
     * @param string $uninstallerFile
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $module,
        string $uninstallerFile,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Uninstaller class for module `%s` (%s) does not implement the UninstallerInterface.', $module, $uninstallerFile)
            : sprintf($message, $module, $uninstallerFile);

        parent::__construct($message, $code, $previous);
    }
}
