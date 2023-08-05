<?php

namespace ForkCMS\Modules\Internationalisation\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class JsTranslationsController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->translator instanceof TranslatorBagInterface) {
            return new JsonResponse(
                ['error' => 'Fork translator not found'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $currentCatalog = $this->translator->getCatalogue();
        $currentLocale = $request->getLocale();
        $translations = [
            'locale' => $currentLocale,
            'translations' => [
                $currentLocale => $currentCatalog->all(),
            ],
        ];

        $fallbackCatalog = $currentCatalog->getFallbackCatalogue();
        if ($fallbackCatalog instanceof MessageCatalogueInterface) {
            $translations['fallback'] = $fallbackCatalog->getLocale();
            $translations['translations'][$translations['fallback']] = $fallbackCatalog->all();
        }

        return new JsonResponse($translations);
    }
}
