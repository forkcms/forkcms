<?php

/**
 * @global string $class_name
 * @global string $namespace
 * @global string $entity
 * @global string $dataTransferObject
 * @global string[] $useStatements
 */

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>

final class <?= $class_name ?> extends <?= $dataTransferObject, PHP_EOL ?>
{
    public function __construct(<?= $entity ?> $<?= lcfirst($entity) ?>)
    {
        parent::__construct($<?= lcfirst($entity) ?>);
    }
}
