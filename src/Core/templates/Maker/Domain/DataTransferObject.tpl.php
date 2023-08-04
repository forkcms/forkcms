<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

abstract class <?= $class_name, PHP_EOL ?>
{
<?php foreach ($fields as $field) { ?>
<?php if ($field['is_generated_value']) {continue;} ?>
<?php if (!$field['is_nullable']) { ?>
    <?= '#[Assert\NotBlank(message: \'err.FieldIsRequired\')]', PHP_EOL ?>
<?php } ?>
    <?= sprintf('public %s%s $%s = null;', $field['is_nullable'] ? '' : '?', $field["type"], $field["name"]), PHP_EOL ?>

<?php } ?>
    protected ?<?= $entity ?> $<?= lcfirst($entity) ?>Entity;

    public function __construct(?<?= $entity ?> $<?= lcfirst($entity) ?>Entity = null)
    {
        $this-><?= lcfirst($entity) ?>Entity = $<?= lcfirst($entity) ?>Entity;
        if (!$<?= lcfirst($entity) ?>Entity instanceof <?= $entity ?>) {
            return;
        }

<?php foreach ($fields as $field) { ?>
<?php if ($field['is_generated_value']) {continue;} ?>
        $this-><?= $field["name"] ?> = $<?= lcfirst($entity) ?>Entity-><?php if ($field["type"] === '\'boolean\'') { echo $field['name'];} else { ?>get<?= ucfirst($field["name"]) ?><?php }?>();
<?php } ?>
    }

    public function hasEntity(): bool
    {
        return $this-><?= lcfirst($entity) ?>Entity === null;
    }

    public function getEntity(): <?= $entity, PHP_EOL ?>
    {
        return $this-><?= lcfirst($entity) ?>Entity;
    }
}
