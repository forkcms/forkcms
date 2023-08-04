<?php

namespace ForkCMS\Core\Domain\Settings;

use Doctrine\ORM\Event\PreFlushEventArgs;
use RuntimeException;

final class SettingsListener
{
    /** @var array<string,mixed> */
    private static array $cache = [];

    public function preFlush(PreFlushEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        foreach ($entityManager->getUnitOfWork()->getIdentityMap() as $className => $entities) {
            if (array_key_exists($className, self::$cache) && count(self::$cache[$className]) === 0) {
                continue;
            }

            $metaData = $entityManager->getClassMetadata($className);
            self::$cache[$className] = [];
            foreach ($metaData->fieldMappings as $field) {
                if ($field['type'] !== 'core__settings__settings_bag') {
                    continue;
                }
                self::$cache[$className][] = $field['fieldName'];
            }

            if (count(self::$cache[$className]) === 0) {
                continue;
            }

            foreach (self::$cache[$className] as $settingsBagFieldName) {
                foreach ($entities as $entity) {
                    /** @phpstan-ignore-next-line */
                    $property = $metaData->getReflectionProperty($settingsBagFieldName)
                        ?? throw new RuntimeException('Property not found');
                    /** @var SettingsBag|null $settings */
                    $settings = $property->getValue($entity);
                    if ($settings === null) {
                        $property->setValue($entity, new SettingsBag());
                    } elseif ($settings->hasChanges()) {
                        $property->setValue($entity, clone $property->getValue($entity));
                    }
                }
            }
        }
    }
}
