<?php

namespace ForkCMS\Modules\Frontend\Backend\Ajax;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Frontend\Domain\Meta\MetaRepository;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class will handle the generating of slugs for meta form types.
 */
final class GenerateSlug extends AbstractAjaxActionController
{
    public function __construct(private readonly MetaRepository $metaRepository)
    {
    }

    protected function execute(Request $request): void
    {
        $slug = $request->request->get('slug', '');
        $className = $request->request->get('className', '');
        $methodName = $request->request->get('methodName', '');
        $parameters = $request->request->get('parameters', '');

        // cleanup values
        $parameters = html_entity_decode($parameters);
        $parameters = @unserialize($parameters, ['allowed_classes' => [Locale::class]]);

        // fetch generated meta url
        $slug = urldecode($this->metaRepository->generateSlug($slug, $className, $methodName, $parameters));
        $this->assign('slug', $slug);
    }
}
