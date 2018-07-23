<?php

namespace Backend\Modules\Tags\Domain\ModuleTag;

use Backend\Modules\Tags\Domain\Tag\Tag;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Backend\Modules\Tags\Domain\Tag\TagRepository")
 * @ORM\Table(name="TagsModuleTag", options={"collate"="utf8_general_ci", "charset"="utf8"})
 */
class ModuleTag
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $moduleName;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $moduleId;

    /**
     * @var Tag
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Backend\Modules\Tags\Domain\Tag\Tag", inversedBy="moduleTags")
     */
    private $tag;

    public function __construct(string $moduleName, int $moduleId, Tag $tag)
    {
        $this->moduleName = $moduleName;
        $this->moduleId = $moduleId;
        $this->tag = $tag;
        $this->tag->increaseNumberOfTimesLinked();
    }

    public function toArray(): array
    {
        return [
            'module' => $this->moduleName,
            'other_id' => (string) $this->moduleId,
            'tag_id' => (string) $this->tag->getId(),
        ];
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}
