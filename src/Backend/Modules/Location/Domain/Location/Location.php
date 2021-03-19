<?php

namespace Backend\Modules\Location\Domain\Location;

use Backend\Modules\Location\Domain\LocationSetting\LocationSetting;
use Common\Exception\CanNotSetExtraIdException;
use Common\Locale;
use Backend\Core\Language\Locale as BackendLocale;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Frontend\Core\Language\Locale as FrontendLocale;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="Backend\Modules\Location\Domain\Location\LocationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Location
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Locale
     *
     * @ORM\Column(type="locale")
     */
    private $locale;

    /**
     * @TODO: map this to some sort of Extra entity
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $extraId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $country;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    private $showInOverview;

    /**
     * @var Collection<LocationSetting>
     *
     * @ORM\OneToMany(
     *     targetEntity="Backend\Modules\Location\Domain\LocationSetting\LocationSetting",
     *     mappedBy="location",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $settings;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedOn;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $overrideMapSettings;

    public function __construct(
        Locale $locale,
        string $title,
        string $street,
        string $number,
        string $zip,
        string $city,
        string $country,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $showInOverview = true,
        int $extraId = null,
        bool $overrideMapSettings = false
    ) {
        $this->locale = $locale;
        $this->title = $title;
        $this->street = $street;
        $this->number = $number;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->showInOverview = $showInOverview;
        $this->extraId = $extraId;
        $this->overrideMapSettings = $overrideMapSettings;

        $this->settings = new ArrayCollection();
    }

    public function update(
        string $title,
        string $street,
        string $number,
        string $zip,
        string $city,
        string $country,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $showInOverview = true,
        bool $overrideMapSettings = false
    ) {
        $this->title = $title;
        $this->street = $street;
        $this->number = $number;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->showInOverview = $showInOverview;
        $this->overrideMapSettings = $overrideMapSettings;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getExtraId(): int
    {
        return $this->extraId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function isShowInOverview(): bool
    {
        return $this->showInOverview;
    }

    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
    }

    public function setExtraId(int $extraId): void
    {
        if ($this->extraId !== null) {
            throw new CanNotSetExtraIdException();
        }

        $this->extraId = $extraId;
    }

    public function addSetting(LocationSetting $setting): void
    {
        $this->settings->add($setting);
    }

    public function isOverrideMapSettings(): bool
    {
        return $this->overrideMapSettings;
    }

    public function setOverrideMapSettings(bool $overrideMapSettings): void
    {
        $this->overrideMapSettings = $overrideMapSettings;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->createdOn = $this->editedOn = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->editedOn = new DateTime();
    }

    public static function fromArray(array $item): self
    {
        if (APPLICATION === 'Frontend') {
            $locale = FrontendLocale::fromString($item['language'] ?? $item['locale']);
        } else {
            $locale = BackendLocale::fromString($item['language'] ?? $item['locale']);
        }

        return new self(
            $locale,
            $item['title'],
            $item['street'],
            $item['number'],
            $item['zip'],
            $item['city'],
            $item['country'],
            $item['lat'] ?? $item['latitude'] ?? null,
            $item['lng'] ?? $item['longitude'] ?? null,
            $item['show_overview'] ?? $item['showInOverview'] ?? true,
            $item['extra_id'] ?? $item['extraId'] ?? null,
            $item['override_map_settings'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'language' => $this->locale->getLocale(),
            'title' => $this->title,
            'street' => $this->street,
            'number' => $this->number,
            'zip' => $this->zip,
            'city' => $this->city,
            'country' => $this->country,
            'lat' => $this->latitude,
            'lng' => $this->longitude,
            'show_overview' => (int) $this->showInOverview,
            'extra_id' => $this->extraId,
            'settings' => $this->settings->toArray(),
            'override_map_settings' => $this->overrideMapSettings,
        ];
    }
}
