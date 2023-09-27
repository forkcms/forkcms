<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\MissingIdentifierField;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessageType;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Backend\Backend\Actions\UserEdit;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionSlug;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGridFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractActionController implements ActionControllerInterface
{
    private string $templatePath;
    /** @var array<string, mixed> */
    private array $twigContext = [];

    protected readonly DataGridFactory $dataGridFactory;
    protected readonly EntityManagerInterface $entityManager;
    protected readonly Environment $twig;
    protected readonly TranslatorInterface $translator;
    protected readonly Header $header;
    protected readonly RouterInterface $router;
    protected readonly FormFactoryInterface $formFactory;
    protected readonly MessageBusInterface $commandBus;
    protected readonly AuthorizationCheckerInterface $authorizationChecker;
    protected readonly ModuleSettings $moduleSettings;
    protected readonly TokenStorageInterface $tokenStorage;

    public function __construct(ActionServices $services)
    {
        $this->dataGridFactory = $services->dataGridFactory;
        $this->entityManager = $services->entityManager;
        $this->twig = $services->twig;
        $this->translator = $services->translator;
        $this->header = $services->header;
        $this->router = $services->router;
        $this->formFactory = $services->formFactory;
        $this->commandBus = $services->commandBus;
        $this->authorizationChecker = $services->authorizationChecker;
        $this->moduleSettings = $services->moduleSettings;
        $this->tokenStorage = $services->tokenStorage;
        $actionSlug = self::getActionSlug();
        $this->templatePath = sprintf(
            '@%s/Backend/Actions/%s.html.twig',
            $actionSlug->getModuleName(),
            $actionSlug->getActionName()
        );
    }

    final protected function changeTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    final public static function getActionSlug(): ActionSlug
    {
        return ActionSlug::fromFQCN(static::class);
    }

    public function __invoke(Request $request): Response
    {
        if ($this->needsRedirectToProfilePage($request)) {
            return $this->redirectToProfilePage($request);
        }

        $this->execute($request);

        return $this->getResponse($request);
    }

    private function redirectToProfilePage(Request $request): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $this->header->addFlashMessage(
            new FlashMessage(
                'msg.2FAIsRequired',
                FlashMessageType::WARNING
            )
        );

        return new RedirectResponse(
            UserEdit::getActionSlug()->generateRoute($this->router) . '/' . $user->getId()
        );
    }

    private function needsRedirectToProfilePage(Request $request): bool
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User === false) {
            return false;
        }

        $currentUrl = $request->getPathInfo();
        $redirectUrl = UserEdit::getActionSlug()->generateRoute($this->router) . '/' . $user->getId();

        $required = $this->moduleSettings->get(
            ModuleName::fromString('Backend'),
            '2fa_required',
            false
        );

        $enabled = $this->moduleSettings->get(
            ModuleName::fromString('Backend'),
            '2fa_enabled',
            false
        );


        if ($required && $enabled && !$user->is2faEnabled() && $currentUrl !== $redirectUrl) {
            return true;
        }

        return false;
    }

    final protected function assign(string $key, mixed $value): void
    {
        $this->twigContext[$key] = $value;
    }

    abstract protected function execute(Request $request): void;

    public function getResponse(Request $request): Response
    {
        return new Response($this->twig->render($this->templatePath, $this->twigContext));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityFQCN
     *
     * @return EntityRepository<T>
     */
    final public function getRepository(string $entityFQCN): EntityRepository
    {
        return $this->entityManager->getRepository($entityFQCN);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityFQCN
     *
     * @return T
     */
    final protected function getEntityFromRequest(Request $request, string $entityFQCN, string $key = 'slug'): mixed
    {
        try {
            return $this->getRepository($entityFQCN)
                ->find(
                    $request->get($key)
                    ?? $request->query->get($key)
                    ?? $request->request->get($key)
                )
                ?? throw new NotFoundHttpException('identifier field not found');
        } catch (MissingIdentifierField) {
            throw new NotFoundHttpException('identifier field not found');
        }
    }

    final protected function isAllowed(ActionSlug|AjaxActionSlug $actionSlug): bool
    {
        return $this->authorizationChecker->isGranted($actionSlug->asModuleAction()->asRole());
    }

    final protected function getModuleName(): ModuleName
    {
        return ModuleName::fromFQCN(static::class);
    }
}
