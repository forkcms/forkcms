<?php

use ForkCMS\Core\Domain\Maker\Util\Entity;

/**
 * @global string $class_name
 * @global string $namespace
 * @global string[] $useStatements
 * @global Entity $entity
*/

?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>

abstract class <?= $class_name, PHP_EOL ?>
{
<?php foreach ($entity->properties as $property) { ?>
    <?php if ($property->isGeneratedValue) {
        continue;
    } ?>
    <?php if (!$property->isNullable) { ?>
        <?= '#[Assert\NotBlank(message: \'err.FieldIsRequired\')]', PHP_EOL ?>
    <?php } ?>
    <?= sprintf('public %s%s $%s = null;', $property->isNullable ? '' : '?', $property->type, $property->name), PHP_EOL ?>

<?php } ?>
    protected ?<?= $entity->getName() ?> $<?= lcfirst($entity->getName()) ?>Entity;

    public function __construct(?<?= $entity->getName() ?> $<?= lcfirst($entity->getName()) ?>Entity = null)
    {
        $this-><?= lcfirst($entity->getName()) ?>Entity = $<?= lcfirst($entity->getName()) ?>Entity;
        if (!$<?= lcfirst($entity->getName()) ?>Entity instanceof <?= $entity->getName() ?>) {
            return;
        }

<?php foreach ($entity->properties as $property) { ?>
    <?php if ($property->isGeneratedValue) {
        continue;
    } ?>
        $this-><?= $property->name ?> = $<?= lcfirst($entity->getName()) ?>Entity-><?php if ($property->type === '\'boolean\'') {
            echo $property->name;
               } else {
                    ?>get<?= ucfirst($property->name) ?><?php
               }?>();
<?php } ?>
    }

    public function hasEntity(): bool
    {
        return $this-><?= lcfirst($entity->getName()) ?>Entity === null;
    }

    public function getEntity(): <?= $entity->getName(), PHP_EOL ?>
    {
        return $this-><?= lcfirst($entity->getName()) ?>Entity;
    }
}
