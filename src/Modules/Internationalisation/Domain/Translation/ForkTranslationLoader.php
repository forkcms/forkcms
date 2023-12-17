<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation;

use DateInterval;
use DateTime;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Error;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use IntlDateFormatter;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

final class ForkTranslationLoader implements LoaderInterface
{
    public function __construct(private readonly TranslationRepository $translationRepository)
    {
    }

    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        $forkLocale = Locale::from($locale);
        $dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        $catalogue = new MessageCatalogue($forkLocale->value);
        try {
            $translationDomain = TranslationDomain::fromDomain($domain);
            $translations = $this->translationRepository->findBy(
                [
                    'locale' => $forkLocale->value,
                    'domain.application' => $translationDomain->getApplication()->value,
                    'domain.moduleName' => $translationDomain->getModuleName(),
                ]
            );
            foreach ($translations as $translation) {
                $catalogue->set((string) $translation->getKey(), $translation->getValue(), $domain);
            }

            $monthDate = new DateTime('first day of January');
            $dayDate = new DateTime('last monday');
            $monthInterval = new DateInterval('P1M');
            $dayInterval = new DateInterval('P1D');
            $possibleDomains = array_filter(Application::cases(), static fn (Application $application): bool =>
                $application->hasEditableTranslations());
            $possibleDomains[] = Application::INSTALLER;

            for ($i = 1; $i <= 12; ++$i) {
                foreach ($possibleDomains as $possibleDomain) {
                    $catalogue->set(
                        'loc.MonthLong' . $i,
                        $dateFormatter->formatObject($monthDate, 'MMMM', $locale),
                        $possibleDomain->value
                    );
                    $catalogue->set(
                        'loc.MonthShort' . $i,
                        $dateFormatter->formatObject($monthDate, 'MMM', $locale),
                        $possibleDomain->value
                    );
                }

                $monthDate->add($monthInterval);
            }
            for ($i = 0; $i < 7; ++$i) {
                foreach ($possibleDomains as $possibleDomain) {
                    $catalogue->set(
                        'loc.DayLong' . $dayDate->format('D'),
                        $dateFormatter->formatObject($dayDate, 'EEEE', $locale),
                        $possibleDomain->value
                    );
                    $catalogue->set(
                        'loc.DayShort' . $dayDate->format('D'),
                        $dateFormatter->formatObject($dayDate, 'EEE', $locale),
                        $possibleDomain->value
                    );
                }

                $dayDate->add($dayInterval);
            }
        } catch (TableNotFoundException | Error) {
            // No translations found
        }

        return $catalogue;
    }
}
