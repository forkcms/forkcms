<?php

namespace ForkCMS\Modules\Backend\Domain\AjaxAction;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface AjaxActionControllerInterface
{
    public function __invoke(Request $request): Response;

    public static function getAjaxActionSlug(): AjaxActionSlug;
}
