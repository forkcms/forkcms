<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Language\Locale;
use Symfony\Component\Validator\Constraints as Assert;

class CreateContentBlock
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
