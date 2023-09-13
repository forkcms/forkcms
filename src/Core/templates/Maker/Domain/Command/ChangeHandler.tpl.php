<?php

/**
 * @global string $class_name
 * @global string $namespace
 * @global string $entity
 * @global string $dataTransferObject
 * @global string $repository
 * @global string $changeCommand
 * @global string[] $useStatements
 */

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>

final class <?= $class_name; ?> implements CommandHandlerInterface
{
    public function __construct(private readonly <?= $repository ?> $<?= lcfirst($repository) ?>)
    {
    }

    public function __invoke(<?= $changeCommand ?> $<?= lcfirst($changeCommand) ?>)
    {
        $<?= lcfirst($entity) ?> = <?= $entity ?>::fromDataTransferObject($<?= lcfirst($changeCommand) ?>);
        $this-><?= lcfirst($repository) ?>->save($<?= lcfirst($entity) ?>);
    }
}
