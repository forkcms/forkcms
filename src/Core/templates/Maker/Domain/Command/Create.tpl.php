<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

final class <?= $class_name ?> extends <?= $dataTransferObject, PHP_EOL ?>
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setEntity(<?= $entity ?> $<?= lcfirst($entity) ?>): void
    {
        $this-><?= lcfirst($entity) ?>Entity = $<?= lcfirst($entity) ?>;
    }
}
