<?php

namespace Console\Exceptions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer interface exception class
 */
class InstallerInterfaceException extends \Exception
{

    /**
     * InstallerInterfaceException constructor.
     * @param string $module
     * @param string $installerFile
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        $module,
        $installerFile,
        $message = '',
        $code = 0,
        \Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Installer class for module `%s` (%s) not implement InstallerInterface.', $module, $installerFile)
            : sprintf($message, $module, $installerFile);

        parent::__construct($message, $code, $previous);
    }
}
