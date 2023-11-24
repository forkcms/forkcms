<?php

namespace ForkCMS\Modules\Extensions\Domain\ThemeTemplate;

use Assert\Assertion;
use Assert\AssertionFailedException;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObject;
use ForkCMS\Core\Domain\Form\Validator\UniqueDataTransferObjectInterface;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/** @implements UniqueDataTransferObjectInterface<ThemeTemplate> */
#[Assert\Callback(callback: 'validateThemeTemplate')]
#[UniqueDataTransferObject(['entityClass' => ThemeTemplate::class, 'fields' => ['name', 'theme']])]
abstract class ThemeTemplateDataTransferObject implements UniqueDataTransferObjectInterface
{
    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public ?string $path = null;

    #[Assert\NotBlank(message: 'err.FieldIsRequired')]
    public SettingsBag $settings;

    public bool $active = true;

    public ?Theme $theme = null;

    public bool $default = false;

    public bool $overwrite = false;

    public function __construct(protected ?ThemeTemplate $themeTemplateEntity = null)
    {
        if (!$themeTemplateEntity instanceof ThemeTemplate) {
            $this->settings = new SettingsBag();

            return;
        }

        $this->name = $themeTemplateEntity->getName();
        $this->path = str_replace(ThemeTemplate::PATH_DIRECTORY, '', $themeTemplateEntity->getPath());
        $this->settings = $themeTemplateEntity->getSettings();
        $this->active = $themeTemplateEntity->getActive();
        $this->theme = $themeTemplateEntity->getTheme();
        $this->default = $themeTemplateEntity->isDefault();
    }

    #[Assert\Regex(
        '/^\[(\/|[a-z0-9])+(,(\/|[a-z0-9]+))*\](,\r?\n?\[(\/|[a-z0-9])+(,(\/|[a-z0-9]+))*\])*$/i',
        'err.InvalidTemplateSyntax'
    )]
    public function getLayout(): string
    {
        return $this->settings->getOr('layout', '');
    }

    public function setLayout(?string $layout): void
    {
        $this->settings->set('layout', str_replace("\r", '', $layout));
    }

    /** @return array<int, array<string, string|array<string, mixed>>> */
    public function getPositions(): array
    {
        return $this->settings->getOr('positions', []);
    }

    /** @param array<int, array<string, string|array<int, int>>> $positions */
    public function setPositions(array $positions): void
    {
        $this->settings->set('positions', $positions);
    }

    public function hasEntity(): bool
    {
        return $this->themeTemplateEntity !== null;
    }

    public function getEntity(): ?ThemeTemplate
    {
        return $this->themeTemplateEntity;
    }

    public function validateThemeTemplate(ExecutionContext $context): void
    {
        $themePath = $this->theme->getPath() . '/' . ThemeTemplate::PATH_DIRECTORY . $this->path;
        $modulePath = __DIR__ . '/../../../Frontend/templates/base/' . $this->path;
        if (!is_file($themePath) && !is_file($modulePath)) {
            $context->buildViolation(TranslationKey::error('TemplateFileNotFound'))
                ->atPath('path')
                ->addViolation();
        }

        $layoutPositions = self::getPositionsFromFormat($this->settings->getOr('layout', ''));
        $positionNames = array_column($this->settings->getOr('positions', []), 'name');
        foreach (array_diff_assoc($positionNames, array_unique($positionNames)) as $index => $duplicatePosition) {
            $context->buildViolation(TranslationKey::error('DuplicatePositionName'), ['%1$s' => $duplicatePosition])
                ->atPath('positions')
                ->atPath(sprintf('[%s]', $index))
                ->atPath('[name]')
                ->addViolation();
        }
        foreach ($positionNames as $index => $position) {
            try {
                Assertion::regex($position, '/^[a-z0-9]+$/i');
            } catch (AssertionFailedException) {
                $context->buildViolation(TranslationKey::error('NoAlphaNumPositionName'), ['%1$s' => $position])
                    ->atPath('positions')
                    ->atPath(sprintf('[%s]', $index))
                    ->atPath('[name]')
                    ->addViolation();
            }
            if (!in_array($position, $layoutPositions, true)) {
                $context->buildViolation(TranslationKey::error('NonExistingPositionName'), ['%1$s' => $position])
                    ->atPath('positions')
                    ->atPath(sprintf('[%s]', $index))
                    ->atPath('[name]')
                    ->addViolation();
            }
        }
    }

    /** @return string[] */
    public static function getPositionsFromFormat(string $format): array
    {
        return array_unique(
            array_filter(
                array_map(
                    static fn ($position): string => preg_replace('/[^a-zA-Z0-9]+/', '', $position),
                    explode(',', $format)
                ),
                strlen(...)
            )
        );
    }
}
