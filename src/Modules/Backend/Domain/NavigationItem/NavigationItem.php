<?php

namespace ForkCMS\Modules\Backend\Domain\NavigationItem;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: NavigationItemRepository::class)]
#[UniqueEntity(fields: ['label', 'slug', 'parent'])]
class NavigationItem
{
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: NavigationItem::class, inversedBy: 'children')]
    private ?self $parent;

    /**
     * @var Collection<int, NavigationItem>|NavigationItem[]
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: NavigationItem::class)]
    #[ORM\OrderBy(['sequence' => 'ASC'])]
    private Collection $children;

    #[ORM\Embedded(class: TranslationKey::class)]
    private TranslationKey $label;

    #[ORM\Column(type: 'modules__backend__action__action_slug', nullable: true)]
    private ?ActionSlug $slug;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $visibleInNavigationMenu;

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private int $sequence;

    public function __construct(
        TranslationKey $label,
        ?ActionSlug $slug = null,
        ?self $parent = null,
        bool $visibleInNavigationMenu = true,
        ?int $sequence = null
    ) {
        $this->label = $label;
        $this->slug = $slug;
        $this->parent = $parent;
        $this->visibleInNavigationMenu = $visibleInNavigationMenu;
        $this->sequence = $sequence ?? $this->getFallbackSequence($parent);
        $this->children = new ArrayCollection();
        if ($parent instanceof self) {
            $parent->children->add($this);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParent(): NavigationItem
    {
        return $this->parent;
    }

    /** @return Collection<int, NavigationItem>|NavigationItem[] */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getLabel(): TranslationKey
    {
        return $this->label;
    }

    public function getSlug(): ?ActionSlug
    {
        return $this->slug;
    }

    public function getModuleAction(): ?ModuleAction
    {
        if ($this->slug === null) {
            return null;
        }

        return $this->slug->asModuleAction();
    }

    public function isVisibleInNavigationMenu(): bool
    {
        return $this->visibleInNavigationMenu;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    private function getFallbackSequence(?self $parent = null): int
    {
        if ($parent === null) {
            throw new InvalidArgumentException('Cannot calculate next sequence, please pass a sequence as an argument');
        }

        return $parent->getChildren()->count() + 1;
    }

    public function getFirstAvailableSlug(): ActionSlug
    {
        return $this->getSlugRecursive() ?? throw new RuntimeException('No slug found');
    }

    private function getSlugRecursive(): ?ActionSlug
    {
        if ($this->slug instanceof ActionSlug) {
            return $this->slug;
        }

        foreach ($this->getChildren() as $navigationItem) {
            $slug = $navigationItem->getSlugRecursive();
            if ($slug instanceof ActionSlug) {
                return $slug;
            }
        }

        return null;
    }
}
