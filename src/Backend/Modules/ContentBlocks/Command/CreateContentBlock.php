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
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $template = ContentBlock::DEFAULT_TEMPLATE;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $text;

    /**
     * @var bool
     */
    public $isVisible = true;

    /**
     * @var ContentBlock
     */
    public $contentBlock;

    /**
     * @var Locale
     */
    public $language;

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
