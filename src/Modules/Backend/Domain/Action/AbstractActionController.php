<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Core\Domain\Util\ArrayUtil;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionSlug;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Pageon\DoctrineDataGridBundle\DataGrid\DataGridFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
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
    protected readonly EventDispatcherInterface $eventDispatcher;

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
        $this->eventDispatcher = $services->eventDispatcher;
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
        $this->execute($request);

        return $this->getResponse($request);
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
        $notFoundException = new NotFoundHttpException('identifier field not found');

        try {
            return $this->getRepository($entityFQCN)
                ->find(
                    $request->get($key)
                    ?? ArrayUtil::flatten($request->query->all())[$key]
                    ?? ArrayUtil::flatten($request->request->all())[$key]
                    ?? throw $notFoundException
                )
                ?? throw $notFoundException;
        } catch (Throwable) {
            throw $notFoundException;
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityFQCN
     *
     * @return T|null
     */
    final protected function getEntityFromRequestOrNull(Request $request, string $entityFQCN, string $key = 'slug'): mixed
    {
        try {
            return $this->getEntityFromRequest($request, $entityFQCN, $key);
        } catch (NotFoundHttpException) {
            return null;
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
