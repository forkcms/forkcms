<?php

namespace Backend\Modules\Pages\Actions;

use Backend\Core\Engine\Base\Action;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\Page\Command\UpdatePage;
use Backend\Modules\Pages\Domain\Page\CopyPageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\Form\CopyPageType;
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

        $this->commandBus->handle($updatePage);

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
            new UpdatePage($this->page)
        );

        $form->handleRequest($this->getRequest());

        return $form;
    }

    private function getBackLink(array $parameters = []): string
    {
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
        $page = $this->pageRepository->findOneBy(
            [
                'id' => $this->getRequest()->query->getInt('id'),
                'status' => Status::active(),
                'locale' => Locale::workingLocale(),
            ]
        );

        if ($page instanceof Page) {
            $this->template->assign('page', $page);

            return $page;
        }

        $this->redirect(
            BackendModel::createUrlForAction('PageIndex') . '&error=non-existing'
        );
    }
}
