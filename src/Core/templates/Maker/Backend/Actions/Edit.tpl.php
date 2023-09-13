<?php

/**
 * @global string $class_name
 * @global string $namespace
 * @global string[] $useStatements
 * @global string $entity
 * @global string $nameField
 */

?>
<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
<?php foreach ($useStatements as $useStatement) {
    echo $useStatement . PHP_EOL;
} ?>
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit an existing <?= $entity, PHP_EOL ?>
 */
final class <?= $class_name ?> extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        /** @var <?= $entity ?> $<?= lcfirst($entity) ?> */
        $<?= lcfirst($entity) ?> = $this->getEntityFromRequest($request, <?= $entity ?>::class);
<?php if ($nameField) { ?>
        $this->header->addBreadcrumb(new Breadcrumb($<?= lcfirst($entity) ?>->get<?= ucfirst($nameField) ?>()));
<?php } ?>

        if ($this->getRepository(<?= $entity ?>::class)->count([]) > 1) {
            $this->addDeleteForm(
                ['id' => $<?= lcfirst($entity) ?>->getId()],
                ActionSlug::fromFQCN(<?= $entity ?>Delete::class)
            );
        }

        return $this->handleForm(
            request: $request,
            formType: <?= $entity ?>Type::class,
            formData: new Change<?= $entity ?>($<?= lcfirst($entity) ?>),
<?php if (!$nameField) { ?>
            flashMessage: FlashMessage::success('Added'),
<?php } ?>
            redirectResponse: new RedirectResponse(<?= $entity ?>Index::getActionSlug()->generateRoute($this->router)),
<?php if ($nameField) { ?>
            flashMessageCallback: static function (Change<?= $entity ?> $changed<?= $entity ?>): FlashMessage {
                return FlashMessage::success('Edited', ['entity' => $changed<?= $entity ?>-><?= $nameField ?>]);
            }
<?php } ?>
        );
    }
}
