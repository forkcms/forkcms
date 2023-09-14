<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Extensions\Domain\InformationFile\Author;
use ForkCMS\Modules\Extensions\Domain\InformationFile\Messages;
use ForkCMS\Modules\Extensions\Domain\InformationFile\Requirements;
use ForkCMS\Modules\Extensions\Domain\InformationFile\SafeHtml;
use ForkCMS\Modules\Extensions\Domain\InformationFile\SafeString;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

#[DataGrid('moduleInformation')]
#[DataGridActionColumn(
    route: 'backend_action',
    routeAttributes: ['module' => 'extensions', 'action' => 'module_detail'],
    routeAttributesCallback: [self::class, 'dataGridSlugCallback'],
    label: 'lbl.Details',
    class: 'btn btn-default btn-sm',
    iconClass: 'fa fa-eye',
    requiredRole: ModuleAction::ROLE_PREFIX . 'EXTENSIONS__MODULE_DETAIL',
    columnAttributes: ['class' => 'fork-data-grid-action'],
)]
final class ModuleInformation
{
    private function __construct(
        #[DataGridPropertyColumn(
            label: 'lbl.Name',
            route: 'backend_action',
            routeAttributes: ['module' => 'extensions', 'action' => 'module_detail'],
            routeAttributesCallback: [self::class, 'dataGridSlugCallback'],
            routeRole: ModuleAction::ROLE_PREFIX . 'EXTENSIONS__MODULE_DETAIL',
            columnAttributes: ['class' => 'title'],
        )]
        public readonly ModuleName $name,
        #[DataGridPropertyColumn(label: 'lbl.Version')]
        public readonly string $version,
        #[DataGridPropertyColumn(label: 'lbl.Description', valueCallback: [self::class, 'truncateDescription'])]
        public readonly ?string $description,
        /** @var Author[] $authors */
        public readonly array $authors,
        /** @var array<int, array<string, string>> $events */
        public readonly array $events,
        public readonly Messages $messages,
    ) {
    }

    public static function fromModule(ModuleName $moduleName): self
    {
        $moduleDirectory = __DIR__ . '/../../../' . $moduleName;

        if (is_dir($moduleDirectory) === false) {
            throw new NotFoundHttpException('The module directory does not exist');
        }

        $path = realpath($moduleDirectory . '/module.xml');

        if ($path === false) {
            return new self(
                $moduleName,
                '1.0.0',
                $moduleName . ' does not have a module.xml file with more information.',
                [],
                [],
                new Messages(),
            );
        }

        return self::fromXML($path);
    }

    public static function fromXML(string $xmlFilePath): self
    {
        try {
            $moduleConfig = simplexml_load_string(
                file_get_contents($xmlFilePath),
                'SimpleXMLElement',
                LIBXML_NOCDATA | LIBXML_ERR_ERROR
            );
        } catch (Throwable) {
            return new self(
                ModuleName::fromString(basename(dirname($xmlFilePath))),
                '?.?.?',
                '',
                [],
                [],
                new Messages([TranslationKey::error('InvalidXML')])
            );
        }
        $messages = new Messages();
        Requirements::fromXML($moduleConfig->requirements, $messages);
        $name = ModuleName::fromString(SafeString::fromXML($moduleConfig->name));
        $directoryName = basename(dirname($xmlFilePath));
        if ($name->getName() !== $directoryName) {
            $messages->addMessage(TranslationKey::error('ModuleNameDoesntMatch'));
        }

        $moduleVersion = SafeString::fromXML($moduleConfig->version)->string;
        if ($moduleVersion === '') {
            $moduleVersion = '1.0.0';
        }

        $authors = [];
        foreach ($moduleConfig->authors->author as $authorConfig) {
            $authors[] = Author::fromXML($authorConfig);
        }

        $events = [];
        if ($moduleConfig->events->event !== null) {
            foreach ($moduleConfig->events->event as $eventConfig) {
                $class = SafeString::fromXML($eventConfig->attributes()->class);
                if (class_exists($class)) {
                    $events[] = [
                        'class' => SafeString::fromXML($eventConfig->attributes()->class)->string,
                        'description' => SafeHtml::fromXML($eventConfig)->html,
                    ];
                }
            }
        }

        return new self(
            $name,
            $moduleVersion,
            SafeHtml::fromXML($moduleConfig->description),
            $authors,
            $events,
            $messages
        );
    }

    public function isInstallable(): bool
    {
        return !$this->messages->hasErrors();
    }

    public static function truncateDescription(string $description): string
    {
        $description = strip_tags($description);
        if (strlen($description) > 100) {
            return substr($description, 0, 100) . '...';
        }

        return $description;
    }

    /**
     * @param array{string?: string} $attributes
     *
     * @return array{string?: string}
     */
    public static function dataGridSlugCallback(self $moduleInformation, array $attributes): array
    {
        $attributes['slug'] = $moduleInformation->name->getName();

        return $attributes;
    }

    public function getModuleName(): string
    {
        return $this->name->getName();
    }
}
