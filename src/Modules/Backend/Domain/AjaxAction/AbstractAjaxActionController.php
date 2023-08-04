<?php

namespace ForkCMS\Modules\Backend\Domain\AjaxAction;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAjaxActionController implements AjaxActionControllerInterface
{
    /** @var array<string, mixed> */
    private array $data = [];

    final public static function getAjaxActionSlug(): AjaxActionSlug
    {
        return AjaxActionSlug::fromFQCN(static::class);
    }

    public function __invoke(Request $request): Response
    {
        $this->execute($request);

        return $this->getResponse($request);
    }

    final protected function assign(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    abstract protected function execute(Request $request): void;

    public function getResponse(Request $request): Response
    {
        return new JsonResponse($this->data);
    }
}
