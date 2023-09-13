<?php

use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use ForkCMS\Core\Domain\Maker\Util\Entity;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Frontend\Domain\Meta\EntityWithMetaTrait;
use Symfony\Bundle\MakerBundle\Str;

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

final class <?= $class_name, PHP_EOL ?>
{
<?php foreach ($entity->properties as $property) { ?>
    <?= $property->isId ? '    #[ORM\Id]' . PHP_EOL : '' ?>
    <?= $property->isGeneratedValue ? '    #[ORM\GeneratedValue]' . PHP_EOL : '' ?>
    <?php if ($property->dbalTypeFull === Embedded::class) { ?>
        <?= sprintf(
            '#[ORM\Embedded(class: %s%s)]',
            trim($property->type, '?') . '::class',
            $property->isNullable ? ', nullable: true' : ''
        ), PHP_EOL ?>
    <?php } elseif ($property->dbalTypeFull === OneToMany::class) { ?>
    <?php } elseif ($property->dbalTypeFull === ManyToMany::class) { ?>
    <?php } elseif ($property->dbalTypeFull === ManyToOne::class) { ?>
    <?php } else { ?>
        <?= sprintf('#[ORM\Column(type: %s)]', $property->dbalType), PHP_EOL ?>
    <?php } ?>
    <?= sprintf('private %s $%s;', $property->type, $property->name), PHP_EOL ?>

<?php } ?>
<?= $entity->hasMeta ? '    use ' . Str::getShortClassName(EntityWithMetaTrait::class) . ';' . PHP_EOL . PHP_EOL : '' ?>
<?= $entity->isBlamable ? '    use ' . Str::getShortClassName(Blameable::class) . ';' . PHP_EOL . PHP_EOL : '' ?>
    private function __construct()
    {
    }

    public static function fromDataTransferObject(<?= $class_name ?>DataTransferObject $dataTransferObject): self
    {
        $entity = $dataTransferObject->hasEntity() ? $dataTransferObject->getEntity() : new self();
<?php foreach ($entity->properties as $property) { ?>
    <?php if ($property->isGeneratedValue) {
        continue;
    } ?>
        $entity-><?= $property->name ?> = $dataTransferObject-><?= $property->name ?>;
<?php } ?>

        return $entity;
    }
<?php foreach ($entity->properties as $property) { ?>
    public function <?php if ($property->type === '\'boolean\'') {
        echo $property->name;
                    } else {
                        ?>get<?= ucfirst($property->name) ?><?php
                    }?>(): <?= $property->type, PHP_EOL ?>
    {
        return $this-><?= $property->name ?>;
    }
<?php } ?>
}
