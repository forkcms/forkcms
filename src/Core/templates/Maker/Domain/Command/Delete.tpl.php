<?php

/**
 * @global string $class_name
 * @global string $namespace
 * @global string $entity
 * @global string $dataTransferObject
 * @global string $idFieldType
 * @global string $idField
 * @global string[] $useStatements
 */

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>

final class <?= $class_name, PHP_EOL ?>
{
    private ?<?= $entity ?> $<?= lcfirst($entity) ?>Entity;

    public function __construct(public readonly <?= $idFieldType ?> $<?= $idField ?>)
    {
    }

    public function getEntity(): <?= $entity, PHP_EOL ?>
    {
        return $this-><?= lcfirst($entity) ?>Entity;
    }

    public function setEntity(<?= $entity ?> $<?= lcfirst($entity) ?>): void
    {
        $this-><?= lcfirst($entity) ?>Entity = $<?= lcfirst($entity) ?>;
    }
}
