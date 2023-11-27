<?php
/**
 * @global string $class_name
 * @global string $namespace
 * @global string $entity
 * @global string $deleteCommand
 * @global string[] $useStatements
 */
?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

final class <?= $class_name ?> extends Event
{
    public function __construct(public readonly <?= $entity ?> $<?= lcfirst($entity) ?>)
    {
    }

    public static function fromDeleteCommand(<?= $deleteCommand ?> $<?= lcfirst($deleteCommand) ?>): self
    {
        return new self($<?= lcfirst($deleteCommand) ?>->getEntity());
    }
}