<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\MissingIdentifierField;
use ForkCMS\Core\Domain\Header\Header;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractBlockController implements BlockControllerInterface
{
    protected readonly EntityManagerInterface $entityManager;
    protected readonly Environment $twig;
    protected readonly TranslatorInterface $translator;
    protected readonly Header $header;
    protected readonly RouterInterface $router;
    protected readonly FormFactoryInterface $formFactory;
    protected readonly MessageBusInterface $commandBus;
    protected readonly AuthorizationCheckerInterface $authorizationChecker;

    protected ?Response $responseOverride = null;
    private string $templatePath;
    private Block $block;
    /** @var array<string, mixed> */
    private array $assignedContent = [];

    public function __construct(BlockServices $services)
    {
        $this->entityManager = $services->entityManager;
        $this->twig = $services->twig;
        $this->translator = $services->translator;
        $this->header = $services->header;
        $this->router = $services->router;
        $this->formFactory = $services->formFactory;
        $this->commandBus = $services->commandBus;
        $this->authorizationChecker = $services->authorizationChecker;
        $moduleBlock = self::getModuleBlock();
        $this->templatePath = sprintf(
            '@%s/Frontend/%s/%s.html.twig',
            $moduleBlock->getModule()->getName(),
            $moduleBlock->getName()->getType()->getDirectoryName(),
            $moduleBlock->getName()->getName(),
        );
    }

    /**
     * @param array<string, mixed> $headers
     *
     * @return string|null always returns null but you can't typehint on that yet
     */
    final public function redirect(
        string $url,
        int $status = Response::HTTP_TEMPORARY_REDIRECT,
        array $headers = []
    ): ?string {
        $this->responseOverride = new RedirectResponse($url, $status, $headers);

        return null;
    }

    final public function getResponseOverride(): ?Response
    {
        return $this->responseOverride;
    }

    final public static function getModuleBlock(): ModuleBlock
    {
        return ModuleBlock::fromFQCN(static::class);
    }

    final protected function changeTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function __invoke(Request $request, Response $response, Block $block): string|array
    {
        $this->block = $block;

        $this->execute($request, $response);

        if ($request->getPreferredFormat() === 'json') {
            return $this->assignedContent;
        }

        return $this->twig->render($this->templatePath, $this->assignedContent);
    }

    final protected function assign(string $key, mixed $value): void
    {
        $this->assignedContent[$key] = $value;
    }

    abstract protected function execute(Request $request, Response $response): void;

    /**
     * @template T of object
     *
     * @param class-string<T> $entityFQCN
     *
     * @return EntityRepository<T>
     */
    public function getRepository(string $entityFQCN): EntityRepository
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
    protected function getEntityFromRequest(Request $request, string $entityFQCN, string $key = 'slug'): mixed
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

    final protected function hasSetting(string $name): bool
    {
        return $this->block->hasSetting($name);
    }

    final protected function getSetting(string $name, mixed $default = null): mixed
    {
        return $this->block->getSetting($name, $default);
    }
}
