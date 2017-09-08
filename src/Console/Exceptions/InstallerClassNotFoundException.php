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
 * Installer class not found exception class
 */
class InstallerClassNotFoundException extends \Exception
{
    /**
     * InstallerClassNotFoundException constructor.
     * @param string $module
     * @param string $installerFile
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $module,
        string $installerFile,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Installer class for module `%s` not found (%s).', $module, $installerFile)
            : sprintf($message, $module, $installerFile);

        parent::__construct($message, $code, $previous);
    }
}
