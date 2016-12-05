<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateContentBlock
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $template = ContentBlock::DEFAULT_TEMPLATE;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="err.FieldIsRequired")
     */
    public $text;

    /**
     * @var bool
     */
    public $isVisible = true;

    /**
     * @var Locale
     */
    public $language;

    /**
     * @var ContentBlock
     */
    public $contentBlock;

    /**
     * @var int
     */
    public $userId;

    /**
     * @param Locale|null $language
     */
    public function __construct(Locale $language = null)
    {
        if ($language === null) {
            $language = Locale::workingLocale();
        }

        $this->language = $language;
    }
}
