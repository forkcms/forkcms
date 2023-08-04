<?php

namespace ForkCMS\Core\Domain\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ForkYamlFileLoader extends YamlFileLoader
{
    /** @return array<string,mixed> */
    protected function loadFile(string $file): array
    {
        $config = array_merge_recursive(
            (array) parent::loadFile(__DIR__ . '/../../../../config/fork_services_defaults.yaml'),
            (array) parent::loadFile($file)
        );

        $calculateBoolean = static function (array $values) {
            return array_reduce(
                $values,
                static fn (bool $carry, bool $current) => $carry && $current,
                true
            );
        };

        if (is_array($config['services']['_defaults']['autowire'])) {
            $config['services']['_defaults']['autowire'] = $calculateBoolean(
                $config['services']['_defaults']['autowire']
            );
        }

        if (is_array($config['services']['_defaults']['autoconfigure'])) {
            $config['services']['_defaults']['autoconfigure'] = $calculateBoolean(
                $config['services']['_defaults']['autoconfigure']
            );
        }

        return $config;
    }
}
