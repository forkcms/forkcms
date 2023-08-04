<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translator;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDomain;
use LogicException;
use Symfony\Component\Translation\DataCollectorTranslator as SymfonyDataCollectorTranslator;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DataCollectorTranslator extends SymfonyDataCollectorTranslator
{
    /** @var array<int, mixed> */
    private array $messages = [];

    private bool $isCollecting = true;

    public function __construct(private TranslatorInterface $translator)
    {
        parent::__construct($this->translator);
    }

    public function getDefaultTranslationDomain(): TranslationDomain
    {
        if (!$this->translator instanceof ForkTranslator) {
            throw new LogicException('Only works with the ForkTranslator.');
        }
        return $this->translator->getDefaultTranslationDomain();
    }

    /** @param array<string, mixed> $parameters */
    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $trans = $this->translator->trans($id = (string) $id, $parameters, $domain, $locale);

        if ($this->translator instanceof ForkTranslator) {
            $domain = $this->translator->getLastUsedDomain();
        }

        if ($this->isCollecting) {
            $this->collectMessage($locale, $domain, $id, $trans, $parameters);
        }

        return $trans;
    }

    public function disableCollecting(): void
    {
        $this->isCollecting = false;
    }

    public function enableCollecting(): void
    {
        $this->isCollecting = true;
    }

    /**
     * @return array<int, mixed>
     */
    public function getCollectedMessages(): array
    {
        return $this->messages;
    }

    /** @param array<string, mixed>|null $parameters */
    private function collectMessage(
        ?string $locale,
        ?string $domain,
        string $id,
        string $translation,
        ?array $parameters = []
    ): void {
        if ($domain === null) {
            $domain = 'messages';
        }

        if (!$this->translator instanceof TranslatorBagInterface) {
            throw new LogicException('Cannot get a catalog out of the translator');
        }
        $catalogue = $this->translator->getCatalogue($locale);
        $locale = $catalogue->getLocale();
        $fallbackLocale = null;
        if ($catalogue->defines($id, $domain)) {
            $state = self::MESSAGE_DEFINED;
        } elseif ($catalogue->has($id, $domain)) {
            $state = self::MESSAGE_EQUALS_FALLBACK;

            $fallbackCatalogue = $catalogue->getFallbackCatalogue();
            while ($fallbackCatalogue) {
                if ($fallbackCatalogue->defines($id, $domain)) {
                    $fallbackLocale = $fallbackCatalogue->getLocale();
                    break;
                }
                $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
            }
        } else {
            $state = self::MESSAGE_MISSING;
        }

        $this->messages[] = [
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'domain' => $domain,
            'id' => $id,
            'translation' => $translation,
            'parameters' => $parameters,
            'state' => $state,
            'transChoiceNumber' => isset($parameters['%count%']) && is_numeric(
                $parameters['%count%']
            ) ? $parameters['%count%'] : null,
        ];
    }
}
