<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Blameable;

#[ORM\Entity(repositoryClass: InstalledLocaleRepository::class)]
#[ORM\Index(columns: ['isDefaultForWebsite'], name: 'idx_default_for_website')]
#[ORM\Index(columns: ['isDefaultForUser'], name: 'idx_default_for_user')]
class InstalledLocale
{
    use EntityWithSettingsTrait;
    use Blameable;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEnabledForWebsite;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDefaultForWebsite;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEnabledForBrowserLocaleRedirect;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEnabledForUser;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDefaultForUser;

    private function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    public static function fromDataTransferObject(InstalledLocaleDataTransferObject $locale): self
    {
        $installedLocale = $locale->hasEntity() ? $locale->getEntity() : new self($locale->locale);

        $installedLocale->isEnabledForWebsite = $locale->isEnabledForWebsite;
        $installedLocale->isDefaultForWebsite = $locale->isDefaultForWebsite;
        $installedLocale->isEnabledForBrowserLocaleRedirect = $locale->isEnabledForBrowserLocaleRedirect;
        $installedLocale->isEnabledForUser = $locale->isEnabledForUser;
        $installedLocale->isDefaultForUser = $locale->isDefaultForUser;
        $installedLocale->settings = new SettingsBag($locale->settings);

        return $installedLocale;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function isEnabledForWebsite(): bool
    {
        return $this->isEnabledForWebsite;
    }

    public function isDefaultForWebsite(): bool
    {
        return $this->isDefaultForWebsite;
    }

    public function isEnabledForBrowserLocaleRedirect(): bool
    {
        return $this->isEnabledForBrowserLocaleRedirect;
    }

    public function isEnabledForUser(): bool
    {
        return $this->isEnabledForUser;
    }

    public function isDefaultForUser(): bool
    {
        return $this->isDefaultForUser;
    }
}
