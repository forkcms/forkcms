<?php

namespace ForkCMS\Core\Domain\Doctrine;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;

trait ForkDBALTypeName
{
    public function getName(): string
    {
        $matches = [];
        if (
            preg_match(
                '/^ForkCMS\\\Modules\\\([A-Z][\w]*)\\\Domain\\\(?:([A-Z][\w]*)\\\)+([A-Z][\w]*)DBALType$/',
                static::class,
                $matches
            )
        ) {
            $matches[0] = 'modules';

            return implode('__', array_map([Container::class, 'underscore'], $matches));
        }

        $matches = [];
        if (
            preg_match(
                '/^ForkCMS\\\Core\\\Domain\\\(?:([A-Z][\w]*)\\\)+([A-Z][\w]*)DBALType$/',
                static::class,
                $matches
            )
        ) {
            $matches[0] = 'core';

            return implode('__', array_map([Container::class, 'underscore'], $matches));
        }

        throw new InvalidArgumentException('Cauld not automatically determine the unique DBAL type name');
    }
}
