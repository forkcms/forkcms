<?php

namespace Backend\Modules\Location\Domain;

use Common\Locale;
use Backend\Core\Language\Locale as BackendLocale;
use Frontend\Core\Language\Locale as FrontendLocale;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity
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
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    private $showInOverview;

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

    public function __construct(
        Locale $locale,
        string $title,
        string $street,
        string $number,
        string $zip,
        string $city,
        string $country,
        float $latitude,
        float $longitude,
        bool $showInOverview = true,
        ?int $extraId = null
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
    }

    public function update(
        int $extraId,
        string $title,
        string $street,
        string $number,
        string $zip,
        string $city,
        string $country,
        float $latitude,
        float $longitude,
        bool $showInOverview = true
    ) {
        $this->extraId = $extraId;
        $this->title = $title;
        $this->street = $street;
        $this->number = $number;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->showInOverview = $showInOverview;
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

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function isShowInOverview(): ?bool
    {
        return $this->showInOverview;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function getEditedOn(): DateTime
    {
        return $this->editedOn;
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
            $locale = FrontendLocale::fromString($item['language']);
        } else {
            $locale = BackendLocale::fromString($item['language']);
        }

        return new self(
            $locale,
            $item['title'],
            $item['street'],
            $item['number'],
            $item['zip'],
            $item['city'],
            $item['country'],
            $item['lat'],
            $item['lng'],
            isset($item['show_overview']) ? (bool) $item['show_overview'] : true,
            isset($item['extra_id']) ? $item['extra_id'] : null
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
        ];
    }
}
