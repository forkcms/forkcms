<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
<?php foreach ($useStatements as $useStatement) echo $useStatement.PHP_EOL ?>

/**
 * Delete an existing <?= $entity, PHP_EOL ?>
 */
final class <?= $class_name ?> extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        return $this->handleDeleteForm(
            $request,
            Delete<?= $entity ?>::class,
            <?= $entity ?>Index::getActionSlug()
        );
    }
}
