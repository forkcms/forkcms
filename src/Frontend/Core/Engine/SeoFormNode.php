<?php

namespace Frontend\Core\Engine;

use Backend\Core\Language\Language as BackendLanguage;
use SpoonFilter;
use Twig\Compiler;
use Twig\Node\Node;

/**
 * Twig node for writing the SEO form
 */
class SeoFormNode extends Node
{
    private $form;

    /**
     * @param string $form Name of the template var holding the form this field
     *                     belongs to.
     * @param int $lineNumber Line number in the template source file.
     * @param string $tag
     */
    public function __construct(string $form, int $lineNumber, string $tag)
    {
        parent::__construct([], [], $lineNumber, $tag);
        $this->form = $form;
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'<fieldset>\';')
            ->write('echo "<legend>' . $this->lbl('Titles') . '</legend>";')
            ->write('echo \'<div class="checkboxTextFieldCombo">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<div class="form-check">\';')
            ->write($this->getField('page_title_overwrite'))
            ->write('echo "<label class=\"form-check-label\" for=\"pageTitleOverwrite\">' . $this->lbl('PageTitle') . '</label>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label for=\"pageTitle\" class=\"visually-hidden\">";')
            ->write('echo "' . $this->lbl('PageTitle') . '</label>";')
            ->write($this->getField('page_title'))
            ->write($this->getError('page_title'))
            ->write('echo "<small class=\"form-text text-muted\">' . $this->msg('HelpPageTitle') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';');

        $compiler
            ->write('if (' . $this->hasField('navigation_title_overwrite') . ') {')
            ->write('echo \'<div class="checkboxTextFieldCombo">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<div class="form-check">\';')
            ->write($this->getField('navigation_title_overwrite'))
            ->write('echo "<label class=\"form-check-label\" for=\"navigationTitleOverwrite\">' . $this->lbl('NavigationTitle') . '</label>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label for=\"navigationTitle\" class=\"visually-hidden\">";')
            ->write('echo "' . $this->lbl('NavigationTitle') . '</label>";')
            ->write($this->getField('navigation_title'))
            ->write($this->getError('navigation_title'))
            ->write('echo "<small class=\"form-text text-muted\">' . $this->msg('HelpNavigationTitle') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'</fieldset>\';')
            ->write('echo \'<fieldset>\';')
            ->write('echo "<legend>' . $this->lbl('MetaInformation') . '</legend>";')
            ->write('echo \'<div class="checkboxTextFieldCombo">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<div class="form-check">\';')
            ->write($this->getField('meta_description_overwrite'))
            ->write('echo "<label class=\"form-check-label\" for=\"metaDescriptionOverwrite\">' . $this->lbl('Description') . '</label>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label for=\"metaDescription\" class=\"visually-hidden\">";')
            ->write('echo "' . $this->lbl('Description') . '</label>";')
            ->write($this->getField('meta_description'))
            ->write($this->getError('meta_description'))
            ->write('echo "<small class=\"form-text text-muted\\">' . $this->msg('HelpMetaDescription') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="checkboxTextFieldCombo">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<div class="form-check">\';')
            ->write($this->getField('meta_keywords_overwrite'))
            ->write('echo "<label class=\"form-check-label\" for=\"metaKeywordsOverwrite\">' . $this->lbl('Keywords') . '</label>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write($this->getError('meta_keywords'))
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label for=\"metaKeywords\" class=\"visually-hidden\">";')
            ->write('echo "' . $this->lbl('Keywords') . '</label>";')
            ->write($this->getField('meta_keywords'))
            ->write('echo "<small class=\"form-text text-muted\">' . $this->msg('HelpMetaKeywords') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label for=\"metaDescriptionOverwrite\">' . $this->lbl('ExtraMetaTags') . '</label>";')
            ->write($this->getField('meta_custom'))
            ->write($this->getError('meta_custom'))
            ->write('echo "<small class=\"form-text text-muted\">' . $this->msg('HelpMetaCustom') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</fieldset>\';')
            ->write('echo \'<fieldset>\';')
            ->write('echo "<legend>' . $this->lbl('URL') . '</legend>";')
            ->write('echo \'<div class="panel-body">\';')
            ->write('echo \'<div class="checkboxTextFieldCombo">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<div class="form-check">\';')
            ->write($this->getField('url_overwrite'))
            ->write('echo "<label class=\"form-check-label\" for=\"urlOverwrite\">' . $this->lbl('URL') . '</label>";')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-inline">\';');

        $compiler
            ->write('echo \'<span id="urlFirstPart">\';')
            ->write('if (' . $this->hasVariable('detailURL') . ') {')
            ->write($this->getVariable('detailURL'))
            ->write('echo "/";')
            ->write('} else {')
            ->write('echo "' . SITE_URL . '/' . ' ' . '";')
            ->write('}')
            ->write('echo \'</span>\';');

        $compiler
            ->write('echo "<label for=\"url\" class=\"visually-hidden\">";')
            ->write('echo "' . $this->lbl('CustomURL') . '</label>";')
            ->write($this->getField('url'))
            ->write('echo \'</div>\';')
            ->write($this->getError('url'))
            ->write('echo "<small class=\"form-text text-muted\">' . $this->msg('HelpMetaURL') . '</small>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</fieldset>\';')
            ->write('echo \'<fieldset>\';')
            ->write('echo "<legend>' . $this->lbl('SEO') . '</legend>";')
            ->write('echo \'<div class="row">\';')
            ->write('echo \'<div class="col-md-6">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label>' . $this->lbl('Index') . '</label>";');

        $compiler
            ->write('if (' . $this->hasError('seo_index') . ') {')
            ->write('echo \'<div class="alert alert-danger">\';')
            ->write($this->getError('seo_index'))
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write($this->loopTroughField('seo_index', '$index'))
            ->write('echo \'<div class="form-check">\';')
            ->write('echo $index["rbtSeoIndex"];')
            ->write('echo "<label for=\"" . $index["id"] . "\">" . $index["label"] . "</label>";')
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="col-md-6">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo "<label>' . $this->lbl('Follow') . '</label>";');

        $compiler
            ->write('if (' . $this->hasError('seo_follow') . ') {')
            ->write('echo \'<div class="alert alert-danger">\';')
            ->write($this->getError('seo_follow'))
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write($this->loopTroughField('seo_follow', '$follow'))
            ->write('echo \'<div class="form-check">\';')
            ->write('echo $follow["rbtSeoFollow"];')
            ->write('echo "<label for=\"" . $follow["id"] . "\">" . $follow["label"] . "</label>";')
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</fieldset>\';')
            ->write($this->getField('meta_id'))
            ->write($this->getField('base_field_name'))
            ->write($this->getField('custom'))
            ->write($this->getField('class_name'))
            ->write($this->getField('method_name'))
            ->write($this->getField('parameters'));
    }

    private function lbl(string $label): string
    {
        return SpoonFilter::ucfirst(BackendLanguage::getLabel($label));
    }

    private function msg(string $message): string
    {
        return BackendLanguage::getMessage($message);
    }

    private function hasVariable(string $variable): string
    {
        return "isset(\$context['{$variable}']) && !empty(\$context['{$variable}'])";
    }

    private function loopTroughField(string $variable, string $as): string
    {
        return "foreach (\$context['{$variable}'] as {$as}) {";
    }

    private function getVariable(string $variable): string
    {
        return "echo \$context['{$variable}'];";
    }

    private function getField(string $fieldName): string
    {
        $form = "\$context['form_{$this->form}']";

        return 'echo ' . $form . "->getField('" . $fieldName . "')->parse();";
    }

    private function hasField(string $fieldName): string
    {
        $form = "\$context['form_{$this->form}']";

        return $form . "->existsField('" . $fieldName . "')";
    }

    private function hasError(string $fieldName): string
    {
        return "\$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() ";
    }

    private function getError(string $fieldName): string
    {
        return "echo \$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() "
            . "? '<span class=\"invalid-feedback\">' "
            . ". \$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() "
            . ". '</span>' : '';";
    }
}
