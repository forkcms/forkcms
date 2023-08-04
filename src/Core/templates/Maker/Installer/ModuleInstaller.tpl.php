<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleInstaller;

final class <?= $class_name; ?> extends ModuleInstaller
{
<?php if ($isRequired): ?>
    public const IS_REQUIRED = true;
<?php endif; ?>
<?php if ($hideFromOverview): ?>
    public const IS_VISIBLE_IN_OVERVIEW = false;
<?php endif; ?>

    public function preInstall(): void
    {
        throw new \RuntimeException('Not implemented yet');
    }

    public function install(): void
    {
        throw new \RuntimeException('Not implemented yet');
    }
}
