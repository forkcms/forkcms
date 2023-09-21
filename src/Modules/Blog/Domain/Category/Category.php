<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Blog\Domain\BlogPost\BlogPost;
use ForkCMS\Modules\Blog\Domain\Category\Command\CategoryDataTransferObject;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'blog__categories')]
#[ORM\HasLifecycleCallbacks]
#[DataGrid('Category')]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: [
        'module' => 'blog',
        'action' => 'category_edit',
    ],
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
    label: 'lbl.Edit',
    class: 'btn btn-primary btn-sm',
    iconClass: 'fa fa-edit',
    requiredRole: ModuleAction::ROLE_PREFIX . 'BLOG__CATEGORY_EDIT',
    columnAttributes: ['class' => 'fork-data-grid-action'],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: 'string')]
    #[DataGridPropertyColumn(
        sortable: true,
        label: 'lbl.Title',
        route: 'backend_action',
        routeAttributes: [
            'module' => 'blog',
            'action' => 'category_edit',
        ],
        routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
        routeRole: ModuleAction::ROLE_PREFIX . 'BLOG__CATEGORY_EDIT',
        columnAttributes: ['class' => 'title'],
    )]
    private string $title;

    // TODO meta

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BlogPost::class)]
    private Collection $posts;

    private function __construct(CategoryDataTransferObject $categoryDataTransferObject)
    {
        $this->locale = $categoryDataTransferObject->locale;
        $this->title = $categoryDataTransferObject->title;
        // TODO meta
    }

    public function update(CategoryDataTransferObject $categoryDataTransferObject): void
    {
        $this->title = $categoryDataTransferObject->title;
        // TODO meta
    }

    public static function fromDataTransferObject(CategoryDataTransferObject $categoryDataTransferObject): self
    {
        return new self($categoryDataTransferObject);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Collection<BlogPost>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    #[DataGridMethodColumn(
        label: 'lbl.NumberOfPosts',
        columnAttributes: ['class' => 'number'],
        // TODO route
    )]
    public function getNumberOfPosts(): int
    {
        return $this->posts->count();
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: int|string}
     */
    public static function dataGridEditLinkCallback(self $category, array $attributes): array
    {
        $attributes['slug'] = $category->getId();

        return $attributes;
    }
}
