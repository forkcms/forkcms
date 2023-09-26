<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use JsonSerializable;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;
use Stringable;

#[ORM\Entity(repositoryClass: ThemeTemplateRepository::class)]
#[DataGrid('ThemeTemplate')]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: [
        'module' => 'extensions',
        'action' => 'theme_template_edit',
    ],
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'],
    label: 'lbl.Edit',
    iconClass: 'edit',
    requiredRole: ModuleAction::ROLE_PREFIX . 'EXTENSIONS__THEME_TEMPLATE_EDIT',
)]
class ThemeTemplate implements JsonSerializable, Stringable
{
    use EntityWithSettingsTrait;

    use Blameable;

    public const PATH_DIRECTORY = 'Frontend/base/';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[DataGridPropertyColumn(label: 'lbl.Name')]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $path;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active;

    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: 'templates')]
    #[ORM\JoinColumn(name: 'theme', referencedColumnName: 'name', nullable: false)]
    private Theme $theme;

    #[ORM\OneToOne(mappedBy: 'defaultTemplate', targetEntity: Theme::class)]
    private ?Theme $defaultForTheme = null;

    private function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public static function fromDataTransferObject(ThemeTemplateDataTransferObject $dataTransferObject): self
    {
        $entity = $dataTransferObject->getEntity() ?? new self($dataTransferObject->theme);
        $entity->name = $dataTransferObject->name;
        $entity->path = self::PATH_DIRECTORY . $dataTransferObject->path;
        $entity->settings = $dataTransferObject->settings;
        $entity->active = $dataTransferObject->active;
        if ($dataTransferObject->default) {
            $entity->theme->getDefaultTemplate()->defaultForTheme = null;
            $entity->defaultForTheme = $entity->theme;
            $entity->theme->changeDefaultTemplate($entity);
        }

        return $entity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    #[DataGridMethodColumn(label: 'lbl.Active')]
    public function getActive(): bool
    {
        return $this->active;
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    #[DataGridMethodColumn(label: 'lbl.Default')]
    public function isDefault(): bool
    {
        return $this->defaultForTheme !== null;
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: int|string}
     */
    public static function dataGridEditLinkCallback(self $themeTemplate, array $attributes): array
    {
        $attributes['slug'] = $themeTemplate->getId();

        return $attributes;
    }

    public function getFullPath(): string
    {
        return $this->theme->getPath() . '/' . $this->path;
    }

    /** @return array<int, array<string, string|array<int, int>>> */
    public function getPositions(): array
    {
        return $this->getSetting('positions', []);
    }

    public function getTemplatePath(): string
    {
        return '@Frontend/base/' . str_replace(self::PATH_DIRECTORY, '', $this->path);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $json = [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'theme' => $this->theme->getName(),
            'is_default' => $this->isDefault(),
            'settings' => $this->settings->all(),
            'has_block' => false,
        ];

        $currentLocale = Locale::current();
        $json['settings']['default_extras'] = $json['settings']['default_extras'][$currentLocale] ?? [];

        // validate
        if (!isset($json['settings']['layout'])) {
            throw new Exception('Invalid template-format.');
        }

        return $json;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
