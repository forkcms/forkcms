<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\Command\ChangeRevision;
use ForkCMS\Modules\Pages\Domain\Revision\Command\CreateRevision;
use ForkCMS\Modules\Pages\Domain\Revision\Form\RevisionType;
use ForkCMS\Modules\Pages\Domain\Revision\Revision;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PageEdit extends AbstractFormActionController
{
    public function __construct(
        ActionServices $actionServices,
        private readonly NavigationBuilder $navigationBuilder
    ) {
        parent::__construct($actionServices);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $revision = $request->query->has('revision')
            ? $this->getEntityFromRequest($request, Revision::class, 'revision')
            : $this->getEntityFromRequest($request, Page::class)->getActiveRevision();

        $this->assign('sidebarTree', $this->navigationBuilder->getTree(Locale::request()));

        if ($request->request->getBoolean('switchTemplate')) {
            $validCallback = function (FormInterface $form): ?FormInterface {
                $this->assign('backend_form', $form->createView());

                return null;
            };
        } else {
            $validCallback = function (FormInterface $form): RedirectResponse {
                /** @var CreateRevision $newRevision */
                $newRevision = $form->getData();
                $newRevision->isDraft = $form->get('saveAsDraft')->isClicked();

                $this->commandBus->dispatch($newRevision);

                return new RedirectResponse(PageIndex::getActionSlug()->generateRoute($this->router));
            };
        }

        return $this->handleForm(
            $request,
            RevisionType::class,
            CreateRevision::fromRevision($revision),
            validCallback: $validCallback
        );
    }
}
