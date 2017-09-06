<?php

namespace Console\Exceptions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Module not exists exception class
 */
class ModuleNotExistsException extends \Exception
{

    /**
     * ModuleNotExistsException constructor.
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
            ? sprintf('Module `%s` does not exist.', $module)
            : sprintf($message, $module);

        parent::__construct($message, $code, $previous);
    }
}
