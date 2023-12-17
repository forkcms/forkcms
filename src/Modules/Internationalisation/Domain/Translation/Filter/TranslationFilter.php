<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Filter;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Symfony\Component\HttpFoundation\Request;

#[DataGrid('translation', noResultsMessage: 'msg.FilterToSeeTheMatchingTranslations')]
final class TranslationFilter
{
    private readonly bool $shouldFilter;

    /**
     * @param Type[] $type
     * @param Locale[] $locale
     */
    private function __construct(
        public ?Application $application,
        public ?ModuleName $moduleName,
        public array $type,
        public array $locale,
        public ?string $name,
        public ?string $value
    ) {
        $this->shouldFilter = $application !== null || $moduleName !== null || $type !== [] ||
            $locale !== [] || $name !== null || $value !== null;
    }

    public static function fromRequest(Request $request): self
    {
        $filter = new self(
            Application::tryFrom($request->query->get('application')),
            $request->query->has('moduleName') ? ModuleName::fromString($request->query->get('moduleName')) : null,
            array_map(Type::from(...), $request->query->all('type')),
            array_map(Locale::from(...), $request->query->all('locale')),
            $request->query->get('name'),
            $request->query->get('value')
        );

        if (count($filter->locale) === 0) {
            $filter->locale = [Locale::current()];
        }

        return $filter;
    }

    public function shouldFilter(): bool
    {
        return $this->shouldFilter;
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }
}
