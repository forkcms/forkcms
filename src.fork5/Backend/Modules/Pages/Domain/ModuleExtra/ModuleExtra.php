<?php

namespace Backend\Modules\Pages\Domain\ModuleExtra;

use Common\Language;
use Doctrine\ORM\Mapping as ORM;
use SpoonFilter;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository")
 * @ORM\Table(name="PagesModuleExtra")
 * @ORM\HasLifecycleCallbacks()
 */
class ModuleExtra
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *     type="integer",
     *     name="id",
     *     options={"comment": "Unique ID for the extra."}
     * )
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     name="module",
     *     options={"comment":"The name of the module this extra belongs to."}
     * )
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(type="module_extra_type", name="type")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     name="label",
     *     options={"comment":"The label for this extra. It will be used for displaying purposes."}
     * )
     */
    private $label;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", name="action", nullable=true)
     */
    private $action;

    /**
     * @var mixed
     *
     * @ORM\Column(
     *     type="text",
     *     name="data",
     *     nullable=true,
     *     options={"comment":"A serialized value with the optional parameters."}
     * )
     */
    private $data;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="hidden")
     */
    private $hidden;

    /**
     * @var int
     *
     * @ORM\Column(
     *     type="integer",
     *     name="sequence",
     *     options={"comment":"The sequence in the backend."}
     * )
     */
    private $sequence;

    public function __construct(
        string $module,
        ModuleExtraType $type,
        string $label,
        ?string $action,
        $data,
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
        ModuleExtraType $type,
        string $label,
        ?string $action,
        $data,
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

    public function getType(): ModuleExtraType
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

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function serialiseData()
    {
        if (!empty($this->data)) {
            $this->data = serialize($this->data);

            return;
        }
        $this->data = null;
    }
    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     * @ORM\PostLoad
     */
    public function unserialiseData()
    {
        if ($this->data === null) {
            $this->data = [];
            return;
        }
        $this->data = unserialize($this->data, ['allowed_classes' => false]);
    }

    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getTranslatedLabel(): string
    {
        $name = $this->data['extra_label'] ?? SpoonFilter::ucfirst(Language::lbl($this->label));

        if (!isset($this->data['label_variables'])) {
            return $name;
        }

        return vsprintf($name, $this->data['label_variables']);
    }
}
