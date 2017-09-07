<?php

namespace Console\Exceptions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Module not installed exception class
 */
class ModuleNotInstalledException extends \Exception
{

    /**
     * ModuleNotInstalledException constructor.
     * @param string $module
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        $module,
        $message = '',
        $code = 0,
        \Throwable $previous = null
    ) {
        $message = '' === $message
            ? sprintf('Module `%s` is not installed.', $module)
            : sprintf($message, $module);

        parent::__construct($message, $code, $previous);
    }
}
