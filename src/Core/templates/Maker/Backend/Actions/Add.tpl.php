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

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add a new <?= $entity, PHP_EOL ?>
 */
final class <?= $class_name ?> extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        return $this->handleForm(
            request: $request,
            formType: <?= $entity ?>Type::class,
            formData: new Create<?= $entity ?>(),
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(<?= $entity ?>Index::getActionSlug()->generateRoute($this->router))
        );
    }
}
