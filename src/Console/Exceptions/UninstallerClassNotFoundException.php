<?php

namespace Console\Exceptions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Uninstaller class not found exception class
 */
class UninstallerClassNotFoundException extends \Exception
{

    /**
     * UninstallerClassNotFoundException constructor.
     * @param string $module
     * @param string $uninstallerFile
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        $module,
        $uninstallerFile,
        $message = '',
        $code = 0,
        \Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Uninstaller class for module `%s` not found (%s).', $module, $uninstallerFile)
            : sprintf($message, $module, $uninstallerFile);

        parent::__construct($message, $code, $previous);
    }
}
