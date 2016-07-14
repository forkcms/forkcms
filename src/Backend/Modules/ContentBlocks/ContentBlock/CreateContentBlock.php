<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Language\LanguageName;
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
     * @var LanguageName
     */
    public $language;

    /**
     * @param LanguageName|null $language
     */
    public function __construct(LanguageName $language = null)
    {
        if ($language === null) {
            $language = LanguageName::workingLanguage();
        }

        $this->language = $language;
    }
}
