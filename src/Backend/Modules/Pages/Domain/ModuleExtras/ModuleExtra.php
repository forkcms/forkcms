<?php

namespace Backend\Modules\Pages\Domain\ModuleExtras;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="modules_extras")
 */
final class ModuleExtra
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="module")
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="type")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="label")
     */
    private $label;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", name="action", nullable=true)
     */
    private $action;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", name="data", nullable=true)
     */
    private $data;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="hidden", nullable=true)
     */
    private $hidden;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="sequence")
     */
    private $sequence;

    public function __construct(
        string $module,
        string $type,
        string $label,
        ?string $action,
        ?string $data,
        bool $hidden,
        int $sequence
    ) {
        $this->module = $module;
        $this->type = $type;
        $this->label = $label;
        $this->action = $action;
        $this->data = $data;
        $this->hidden = $hidden;
        $this->sequence = $sequence;
    }

    public function update(
        string $module,
        string $type,
        string $label,
        ?string $action,
        ?string $data,
        bool $hidden,
        int $sequence
    ): void {
        $this->module = $module;
        $this->type = $type;
        $this->label = $label;
        $this->action = $action;
        $this->data = $data;
        $this->hidden = $hidden;
        $this->sequence = $sequence;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function unserializedData(): ?string
    {
        if ($this->getData() === null) {
            return null;
        }

        return unserialize($this->getData());
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }
}
