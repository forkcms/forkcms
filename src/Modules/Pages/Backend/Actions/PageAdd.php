<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\ThemeTemplate\ThemeTemplateRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Pages\Domain\Page\NavigationBuilder;
use ForkCMS\Modules\Pages\Domain\Page\Page;
use ForkCMS\Modules\Pages\Domain\Revision\Command\CreateRevision;
use ForkCMS\Modules\Pages\Domain\Revision\Form\RevisionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PageAdd extends AbstractFormActionController
{
    public function __construct(
        ActionServices $actionServices,
        private readonly NavigationBuilder $navigationBuilder,
        private readonly ThemeTemplateRepository $themeTemplateRepository
    ) {
        parent::__construct($actionServices);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $this->assign('sidebarTree', $this->navigationBuilder->getTree(Locale::current()));

        $page = new Page(Locale::current());
        if ($request->request->getBoolean('switchTemplate')) {
            $validCallback = function (FormInterface $form): ?FormInterface {
                $this->assign('backend_form', $form->createView());

                return null;
            };
        } else {
            $validCallback = function (FormInterface $form): RedirectResponse {
                $this->commandBus->dispatch($form->getData());

                return new RedirectResponse(
                    PageEdit::getActionSlug()->generateRoute(
                        $this->router,
                        [
                            'slug' => $form->getData()->page->getId()
                        ]
                    )
                );
            };
        }

        return $this->handleForm(
            $request,
            RevisionType::class,
            CreateRevision::new($page, Locale::current(), $this->themeTemplateRepository->findDefaultTemplate()),
            validCallback: $validCallback
        );
    }
}
