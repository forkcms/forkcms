<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use ForkCMS\Core\Domain\Form\ActionType;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Extensions\Domain\Module\Command\ChangeModuleSettings;
use ForkCMS\Modules\Extensions\Domain\Module\Module;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translator\DataCollectorTranslator;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFormActionController extends AbstractActionController
{
    protected function execute(Request $request): void
    {
        $this->addBreadcrumbForRequest($request);
    }

    protected function addBreadcrumbForRequest(Request $request): void
    {
        if ($this->translator instanceof DataCollectorTranslator) {
            $this->translator->disableCollecting();
        }
        $actionLabel = self::getActionSlug()->getActionName()->asLabel();
        $translatedActionName = $this->translator->trans($actionLabel);
        if ($this->translator instanceof DataCollectorTranslator) {
            $this->translator->enableCollecting();
        }
        if (str_starts_with($translatedActionName, 'lbl.')) {
            $label = match (true) {
                str_ends_with($translatedActionName, 'Edit') => 'Edit',
                str_ends_with($translatedActionName, 'Add') => 'Add',
                default => null,
            };
            // translate it again, even if we didn't change it so it is collected if needed
            $translatedActionName = $label === null
                ? $actionLabel : $this->translator->trans(TranslationKey::label($label));
        }
        $this->header->addBreadcrumb(new Breadcrumb($translatedActionName, $request->getRequestUri()));
    }

    public function getResponse(Request $request): Response
    {
        return $this->getFormResponse($request) ?? parent::getResponse($request);
    }

    abstract protected function getFormResponse(Request $request): ?Response;

    /**
     * @codingStandardsIgnoreStart
     * @param class-string<FormTypeInterface> $formType
     * @param array<string, mixed> $formOptions
     * @param callable(FormInterface):Response|callable(FormInterface):FormInterface|callable(FormInterface):null|null $defaultCallback
     * @param callable(FormInterface):Response|callable(FormInterface):FormInterface|callable(FormInterface):null|null $validCallback
     * @param callable(FormInterface):FlashMessage|null $flashMessageCallback
     * @codingStandardsIgnoreEnd
     */
    final protected function handleForm(
        Request $request,
        string $formType,
        object $formData = null,
        FlashMessage $flashMessage = null,
        ?RedirectResponse $redirectResponse = null,
        array $formOptions = [],
        ?callable $defaultCallback = null,
        ?callable $validCallback = null,
        ?callable $flashMessageCallback = null,
    ): Response|FormInterface|null {
        $defaultCallback ??= function (FormInterface $form): ?FormInterface {
            $this->assign('backend_form', $form->createView());

            return null;
        };
        $validCallback ??= function (FormInterface $form) use ($redirectResponse): ?Response {
            $this->commandBus->dispatch($form->getData());

            return $redirectResponse;
        };

        $form = $this->formFactory->create($formType, $formData, $formOptions);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = $validCallback($form);
            if ($flashMessage instanceof FlashMessage) {
                $this->header->addFlashMessage($flashMessage);
            } elseif (is_callable($flashMessageCallback)) {
                $this->header->addFlashMessage($flashMessageCallback($form));
            }

            return $response;
        }

        return $defaultCallback($form);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    final protected function addDeleteForm(
        array $data,
        ActionSlug $deleteActionSlug,
        string $formType = ActionType::class,
        array $options = []
    ): void {
        $this->assign('crudDeleteAction', $deleteActionSlug->getActionName());
        $this->assign(
            'backend_delete_form',
            $this->formFactory->create(
                $formType,
                $data,
                array_merge(['actionSlug' => $deleteActionSlug], $options)
            )->createView()
        );
    }

    /**
     * @param class-string<FormTypeInterface> $formType
     */
    final protected function handleSettingsForm(
        Request $request,
        string $formType,
        object $formData
    ): Response|FormInterface|null {
        return $this->handleForm(
            $request,
            $formType,
            $formData,
            FlashMessage::success('SettingsSaved'),
            new RedirectResponse(self::getActionSlug()->generateRoute($this->router))
        );
    }

    /**
     * @param class-string<FormTypeInterface> $formType
     * @param array<string, mixed> $defaults
     */
    final protected function handleModuleSettingsForm(
        Request $request,
        string $formType,
        array $defaults = []
    ): Response|FormInterface|null {
        $moduleRepository = $this->getRepository(Module::class);
        $moduleName = $this->getModuleName();

        return $this->handleSettingsForm(
            $request,
            $formType,
            new ChangeModuleSettings(
                $moduleRepository->find(ModuleName::core()) ?? throw new RuntimeException('Core module not found'),
                $moduleRepository->find($moduleName) ?? throw new RuntimeException($moduleName . ' module not found'),
                $defaults
            ),
        );
    }
}
