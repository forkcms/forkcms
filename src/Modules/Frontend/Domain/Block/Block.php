<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Core\Domain\Settings\EntityWithSettingsTrait;
use ForkCMS\Core\Domain\Settings\SettingsBag;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[ORM\Entity(repositoryClass: BlockRepository::class)]
class Block implements TranslatableInterface
{
    use EntityWithSettingsTrait;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Embedded]
    private ModuleBlock $block;

    #[ORM\Column(type: Types::STRING, enumType: Type::class)]
    #[Gedmo\SortableGroup]
    private Type $type;

    #[ORM\Embedded]
    private TranslationKey $label;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $hidden;

    #[ORM\Column(type: Types::INTEGER)]
    #[Gedmo\SortablePosition]
    /** @phpstan-ignore-next-line */
    private ?int $position;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true, enumType: Locale::class)]
    private ?Locale $locale = null;

    public function __construct(
        ModuleBlock $block,
        ?TranslationKey $label = null,
        ?SettingsBag $settings = null,
        bool $hidden = false,
        ?int $position = null,
        ?Locale $locale = null,
    ) {
        $this->block = $block;
        $this->type = $block->getName()->getType();
        $this->settings = $settings ?? new SettingsBag();
        $this->label = $label ?? TranslationKey::label($block->getName()->getName());
        $this->hidden = $hidden;
        $this->position = $position;
        $this->locale = $locale;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSettings(): SettingsBag
    {
        return $this->settings;
    }

    public function getBlock(): ModuleBlock
    {
        return $this->block;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    public function getLabel(): TranslationKey
    {
        return $this->label;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function hide(): void
    {
        $this->hidden = true;
    }

    public function show(): void
    {
        $this->hidden = false;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function changePosition(int $position): void
    {
        $this->position = $position;
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        $module = $this->block->getModule()->asLabel()->trans($translator) . ' › ';
        $hasOverwrite = $this->settings->has('label');
        $hasLocaleSpecificOverwrite = $this->settings->has('label_' . $locale);
        if (!$hasOverwrite && !$hasLocaleSpecificOverwrite) {
            return $module . $this->label->trans($translator);
        }

        $overwriteSettingName = $hasLocaleSpecificOverwrite ? 'label_' . $locale : 'label';

        if ($this->settings->has($overwriteSettingName . '_parameters')) {
            return $module . vsprintf(
                $this->settings->get($overwriteSettingName),
                $this->settings->get($overwriteSettingName . '_parameters')
            );
        }

        return $module . $this->settings->get($overwriteSettingName);
    }

    public function __toString(): string
    {
        return $this->getBlock()->getFQCN();
    }
}
