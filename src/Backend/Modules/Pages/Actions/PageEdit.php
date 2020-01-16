<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\Action;
use Backend\Core\Language\Locale;
use Backend\Form\Type\DeleteType;
use Backend\Modules\Pages\Domain\Page\Command\UpdatePage;
use Backend\Modules\Pages\Domain\Page\CopyPageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\Form\CopyPageToOtherLanguageType;
use Backend\Modules\Pages\Domain\Page\Form\PageType;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Common\ModulesSettings;
use SimpleBus\Message\Bus\MessageBus;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\KernelInterface;

final class PageEdit extends Action
{
    /** @var ModulesSettings */
    private $settings;

    /** @var PageRepository */
    private $pageRepository;

    /** @var MessageBus */
    private $commandBus;

    /** @var Page */
    private $page;

    public function setKernel(KernelInterface $kernel = null): void
    {
        parent::setKernel($kernel);

        $this->settings = $this->getContainer()->get('fork.settings');
        $this->pageRepository = $this->getContainer()->get(PageRepository::class);
        $this->commandBus = $this->getContainer()->get('command_bus');
    }

    public function execute(): void
    {
        parent::execute();

        $this->page = $this->getPage();
        $this->createCopyToOtherLocaleForm();
        $this->createDeleteForm();
        $this->header->appendDetailToBreadcrumbs($this->page->getTitle());

        $form = $this->getForm();

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->template->assign('form', $form->createView());

            $this->parse();
            $this->display();

            return;
        }

        $this->handleForm($form);
    }

    private function handleForm(Form $form): void
    {
        $updatePage = $form->getData();

        $updatePage->status = $form->get('saveAsDraft')->isClicked() ? Status::draft() : Status::active();

        $this->commandBus->handle($updatePage);

        if ($updatePage->status->isDraft()) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'report' => 'saved-as-draft',
                        'var' => rawurlencode($updatePage->title),
                        'highlight-row' => $updatePage->id,
                        'draft' => $updatePage->revisionId,
                    ]
                )
            );
        }

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'edited',
                    'var' => $updatePage->title,
                ]
            )
        );
    }

    private function getForm(): Form
    {
        $form = $this->createForm(
            PageType::class,
            new UpdatePage($this->page, $this->getRequest()->query->getInt('template-id'))
        );

        $form->handleRequest($this->getRequest());

        return $form;
    }

    private function getBackLink(array $parameters = []): string
    {
        $parameters['id'] = $parameters['id'] ?? $this->page->getId();

        return BackendModel::createUrlForAction(
            'PageEdit',
            null,
            null,
            $parameters
        );
    }

    protected function parse(): void
    {
        $this->header->addJS('/js/vendors/jstree.js', null, false, true, true);
        $this->template->assign('tree', BackendPagesModel::getTreeHTML());
    }

    private function getPage(): ?Page
    {
        $query = $this->getRequest()->query;
        $parameters = [
            'id' => $query->getInt('id'),
            'status' => Status::active(),
            'locale' => Locale::workingLocale(),
        ];

        if ($query->has('revision')) {
            $parameters['revisionId'] = $query->getInt('revision');
            $parameters['status'] = Status::archive();
        } elseif ($query->has('draft')) {
            $parameters['revisionId'] = $query->getInt('draft');
            $parameters['status'] = Status::draft();
            $parameters['userId'] = Authentication::getUser()->getUserId();
        }

        $page = $this->pageRepository->findOneBy($parameters);

        if ($page instanceof Page) {
            $this->template->assign('page', $page);

            return $page;
        }

        $this->redirect(
            BackendModel::createUrlForAction('PageIndex') . '&error=non-existing'
        );
    }

    private function createCopyToOtherLocaleForm(): void
    {
        $copyForm = $this->createForm(
            CopyPageToOtherLanguageType::class,
            new CopyPageDataTransferObject(Locale::workingLocale(), $this->page)
        );
        $this->template->assign('copyToOtherLanguageForm', $copyForm->createView());
    }

    private function createDeleteForm(): void
    {
        if (
            !$this->page->isAllowDelete()
            || !Authentication::isAllowedAction('PageDelete', $this->getModule())
            || $this->pageRepository->getFirstChild(
                $this->page->getId(),
                Status::active(),
                Locale::workingLocale()
            ) instanceof Page
        ) {
            return;
        }

        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $this->page->getId()],
            ['module' => $this->getModule(), 'action' => 'PageDelete']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());
    }
}
