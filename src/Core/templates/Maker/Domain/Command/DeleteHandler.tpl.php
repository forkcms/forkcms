<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>
use InvalidArgumentException;

final class <?= $class_name ?> implements CommandHandlerInterface
{
    public function __construct(private readonly <?= $repository ?> $<?= lcfirst($repository) ?>)
    {
    }

    public function __invoke(<?= $deleteCommand ?> $<?= lcfirst($deleteCommand) ?>)
    {
        $<?= lcfirst($entity) ?> = $this-><?= lcfirst($repository) ?>->find($<?= lcfirst($deleteCommand) ?>-><?= $idField ?>)  ?? throw new InvalidArgumentException('Entity not found');
        $this-><?= lcfirst($repository) ?>->remove($<?= lcfirst($entity) ?>);
        $<?= lcfirst($deleteCommand) ?>->setEntity($<?= lcfirst($entity) ?>);
    }
}
