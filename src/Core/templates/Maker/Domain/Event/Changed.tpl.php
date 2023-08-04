<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

final class <?= $class_name ?> extends Event
{
    public function __construct(public readonly <?= $entity ?> $<?= lcfirst($entity) ?>)
    {
    }

    public static function fromChangeCommand(<?= $changeCommand ?> $<?= lcfirst($changeCommand) ?>): self
    {
        return new self($<?= lcfirst($changeCommand) ?>->getEntity());
    }
}
