<?php

/**
 * @global string $class_name
 * @global string $namespace
 * @global string[] $useStatements
 * @global string $entity
 */

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>
use Symfony\Component\HttpFoundation\Request;

/**
 * <?= $entity ?> overview
 */
final class <?= $class_name ?> extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        $this->renderDataGrid(<?= $entity ?>::class);
    }
}
