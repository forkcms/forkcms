<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\ActionControllerInterface;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Internationalisation\Domain\Exporter\Exporter;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\TranslationFilter;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TranslationExport implements ActionControllerInterface
{
    public function __construct(
        private readonly TranslationRepository $translationRepository,
        private readonly Exporter $exporter
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $filter = TranslationFilter::fromRequest($request);

        $queryBuilder = $this->translationRepository->getTranslationsQueryBuilderForFilter($filter);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'translations.xml'
        );

        return new Response(
            $this->exporter->export(
                $queryBuilder
                    ->orderBy('t.domain.application', 'ASC')
                    ->addOrderBy('t.domain.moduleName', 'ASC')
                    ->addOrderBy('t.key.type', 'ASC')
                    ->addOrderBy('t.key.name', 'ASC')
                    ->addOrderBy('t.source', 'ASC')
                    ->getQuery()
                    ->toIterable(),
                'xml'
            ),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => $disposition,
            ]
        );
    }

    public static function getActionSlug(): ActionSlug
    {
        return ActionSlug::fromFQCN(self::class);
    }
}
