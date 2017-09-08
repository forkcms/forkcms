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
 * Module already installed exception class
 */
class ModuleAlreadyInstalledException extends \Exception
{
    /**
     * ModuleAlreadyInstalledException constructor.
     * @param string $module
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $module,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Module `%s` already installed.', $module)
            : sprintf($message, $module);

        parent::__construct($message, $code, $previous);
    }
}
