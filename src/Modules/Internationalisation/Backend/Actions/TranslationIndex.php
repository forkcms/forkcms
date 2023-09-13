<?php

namespace ForkCMS\Modules\Internationalisation\Backend\Actions;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Backend\Ajax\TranslationEdit as AjaxTranslationEdit;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\FilteredTranslation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\TranslationFilter;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Filter\TranslationFilterType;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationRepository;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Pageon\DoctrineDataGridBundle\Column\Column;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Filterable overview of translations
 */
final class TranslationIndex extends AbstractFormActionController
{
    private TranslationFilter $filter;

    public function __construct(
        ActionServices $actionServices,
        private readonly TranslationRepository $translationRepository
    ) {
        parent::__construct($actionServices);
    }

    protected function addBreadcrumbForRequest(Request $request): void
    {
        // no action specific breadcrumb needed
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $this->filter = TranslationFilter::fromRequest($request);

        if ($this->filter->shouldFilter() && $this->isAllowed(TranslationExport::getActionSlug())) {
            $this->assign(
                'exportUrl',
                TranslationExport::getActionSlug()
                    ->withDefaultParameters($this->filter->toArray())
                    ->generateRoute($this->router)
            );
        }

        $redirectResponse = $this->handleForm(
            $request,
            TranslationFilterType::class,
            $this->filter,
            validCallback: function (FormInterface $form) use ($request): RedirectResponse {
                $request->getSession()->remove(TranslationFilter::class);

                return new RedirectResponse(
                    self::getActionSlug()->generateRoute($this->router, $form->getData()->toArray())
                );
            }
        );

        if ($redirectResponse instanceof RedirectResponse) {
            return $redirectResponse;
        }

        if (!$this->filter->shouldFilter()) {
            $this->assign(
                'notFiltered',
                $this->dataGridFactory->forArray(TranslationFilter::class, [])
            );

            return null;
        }

        $filteredTranslations = $this->translationRepository->getFilteredTranslations($this->filter);

        if (count($filteredTranslations) === 0) {
            $this->assign(
                'notFiltered',
                $this->dataGridFactory->forArray(FilteredTranslation::class, [])
            );

            return null;
        }

        foreach ($filteredTranslations as $type => $translations) {
            $filteredTranslations[$type] = [
                'type' => Type::from($type),
                'dataGrid' => $this->dataGridFactory->forArray(
                    FilteredTranslation::class,
                    $translations,
                    PHP_INT_MAX,
                    [],
                    null,
                    ...$this->getExtraColumns()
                ),
            ];
        }
        $this->assign('dataGrids', $filteredTranslations);

        return null;
    }

    /** @return Column[] */
    private function getExtraColumns(): array
    {
        $columns = [];
        if ($this->filter->application === null) {
            $columns[] = Column::createPropertyColumn(
                'application',
                'lbl.Application',
                null,
                false,
                false,
                order: 0,
                valueCallback: $this->translateApplication(...)
            );
        }
        if ($this->filter->moduleName === null) {
            $columns[] = Column::createPropertyColumn(
                'moduleName',
                'lbl.Module',
                null,
                false,
                false,
                order: 0,
                valueCallback: $this->translateModuleName(...)
            );
        }

        $columns[] = Column::createPropertyColumn(
            'name',
            'lbl.ReferenceCode',
            null,
            false,
            false,
            order: 0,
            valueCallback: $this->markNameFilter(...),
            html: true
        );

        foreach ($this->filter->locale as $locale) {
            $columns[] = new Column(
                $locale->value,
                $locale->asTranslatable(),
                valueCallback: $this->translationValue(...),
                html: true,
                columnAttributesCallback: static function (
                    FilteredTranslation $translation,
                    array $attributes
                ) use (
                    $locale
                ) {
                    if ($translation->getValue($locale) === '') {
                        $attributes['class'] = 'highlighted';
                    }

                    return $attributes;
                }
            );
        }

        if (count($this->filter->locale) === 1) {
            if ($this->isAllowed(TranslationAdd::getActionSlug())) {
                $columns[] = Column::createActionColumn(
                    label: 'lbl.Copy',
                    route: 'backend_action',
                    routeAttributes: TranslationAdd::getActionSlug()->getRouteParameters() + $this->filter->toArray(),
                    routeAttributesCallback: $this->addTranslationSlug(...),
                    class: 'btn btn-default btn-sm',
                    iconClass: 'fa fa-copy',
                    columnAttributes: ['class' => 'fork-data-grid-action'],
                );
            }
            if ($this->isAllowed(TranslationEdit::getActionSlug())) {
                $columns[] = Column::createActionColumn(
                    label: 'lbl.Edit',
                    route: 'backend_action',
                    routeAttributes: TranslationEdit::getActionSlug()->getRouteParameters() + $this->filter->toArray(),
                    routeAttributesCallback: $this->addTranslationSlug(...),
                    class: 'btn btn-primary btn-sm',
                    iconClass: 'fa fa-edit',
                    columnAttributes: ['class' => 'fork-data-grid-action'],
                );
            }
        }

        return $columns;
    }

    public function translateApplication(Application $application): string
    {
        return ucfirst($application->trans($this->translator));
    }

    public function translateModuleName(ModuleName $moduleName): string
    {
        if ($moduleName === ModuleName::core()) {
            return '';
        }

        return ucfirst($moduleName->asLabel()->trans($this->translator));
    }

    public function markNameFilter(string $name): string
    {
        $name = $this->sanitiseHTML($name);

        if ($this->filter->name === null) {
            return $name;
        }

        return preg_replace('/.*?(' . $this->filter->name . ').*?/i', '<mark class="px-0">$1</mark>', $name);
    }

    public function translationValue(
        string $translation,
        FilteredTranslation $filteredTranslation,
        string $locale
    ): string {
        $translation = $this->sanitiseHTML($translation);
        if ($this->filter->value !== null) {
            $translation = preg_replace(
                '/(.*?)(' . $this->filter->value . ')(.*?)/is',
                '$1<mark class="px-0">$2</mark>$3',
                $translation
            );
        }

        if ($this->isAllowed(AjaxTranslationEdit::getAjaxActionSlug())) {
            return sprintf(
                '<span data-role="ajax-content-editable" data-ajax-editable-url="%1$s">%2$s</span>',
                AjaxTranslationEdit::getAjaxActionSlug()->generateRoute(
                    $this->router,
                    [
                        'application' => $filteredTranslation->application->value,
                        'moduleName' => $filteredTranslation->moduleName,
                        'type' => $filteredTranslation->type->value,
                        'name' => $filteredTranslation->name,
                        'locale' => $locale,
                    ]
                ),
                $translation
            );
        }

        return $translation;
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: string}
     */
    public function addTranslationSlug(FilteredTranslation $filteredTranslation, array $attributes): array
    {
        $attributes['slug'] = $filteredTranslation->getId(reset($this->filter->locale));

        return $attributes;
    }

    public function sanitiseHTML(string $html): string
    {
        static $sanitizer;
        if ($sanitizer === null) {
            $sanitizer = new HtmlSanitizer(new HtmlSanitizerConfig());
        }

        return $sanitizer->sanitizeFor('title', $html);
    }
}
