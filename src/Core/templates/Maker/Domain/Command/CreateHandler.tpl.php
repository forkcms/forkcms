<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

final class <?= $class_name ?> implements CommandHandlerInterface
{
    public function __construct(private readonly <?= $repository ?> $<?= lcfirst($repository) ?>)
    {
    }

    public function __invoke(<?= $createCommand ?> $<?= lcfirst($createCommand) ?>)
    {
        $<?= lcfirst($entity) ?> = <?= $entity ?>::fromDataTransferObject($<?= lcfirst($createCommand) ?>);
        $this-><?= lcfirst($repository) ?>->save($<?= lcfirst($entity) ?>);
        $<?= lcfirst($createCommand) ?>->setEntity($<?= lcfirst($entity) ?>);
    }
}
