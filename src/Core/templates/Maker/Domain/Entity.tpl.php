<?php

use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

final class <?= $class_name, PHP_EOL ?>
{
<?php foreach ($fields as $field) { ?>
<?= $field['is_id'] ? '    #[ORM\Id]' . PHP_EOL : '' ?>
<?= $field['is_generated_value'] ? '    #[ORM\GeneratedValue]' . PHP_EOL : '' ?>
<?php if ($field['dbal_type_full'] === Embedded::class) { ?>
    <?= sprintf(
        '#[ORM\Embedded(class: %s%s)]',
        trim($field["type"], '?') . '::class',
        $field['is_nullable'] ? ', nullable: true' : ''
    ), PHP_EOL ?>
<?php } elseif ($field['dbal_type_full'] === OneToMany::class) { ?>
<?php } elseif ($field['dbal_type_full'] === ManyToMany::class) { ?>
<?php } elseif ($field['dbal_type_full'] === ManyToOne::class) { ?>
<?php } else { ?>
    <?= sprintf('#[ORM\Column(type: %s)]', $field["dbal_type"]), PHP_EOL ?>
<?php } ?>
    <?= sprintf('private %s $%s;', $field["type"], $field["name"]), PHP_EOL ?>

<?php } ?>
<?= $meta ? '    use ' . $meta . ';' . PHP_EOL . PHP_EOL : '' ?>
<?= $blameable ? '    use ' . $blameable . ';'. PHP_EOL. PHP_EOL : '' ?>
    private function __construct()
    {
    }

    public static function fromDataTransferObject(<?= $class_name ?>DataTransferObject $dataTransferObject): self
    {
        $entity = $dataTransferObject->isNew() ? new self() : $dataTransferObject->getEntity();
<?php foreach ($fields as $field) { ?>
<?php if ($field['is_generated_value']) {continue;} ?>
        $entity-><?= $field["name"] ?> = $dataTransferObject-><?= $field["name"] ?>;
<?php } ?>

        return $entity;
    }
<?php foreach ($fields as $field) { ?>

    public function <?php if ($field["type"] === '\'boolean\'') { echo $field['name'];} else { ?>get<?= ucfirst($field["name"]) ?><?php }?>(): <?= $field["type"], PHP_EOL ?>
    {
        return $this-><?= $field["name"] ?>;
    }
<?php } ?>
}
