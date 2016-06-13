<?php

namespace Frontend\Core\Engine;

use Backend\Core\Engine\Language as BackendLanguage;

/**
 * Twig node for writing the SEO form
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class SeoFormNode extends \Twig_Node
{
    private $form;

    /**
     * @param string $form Name of the template var holding the form this field
     *                     belongs to.
     * @param int $lineno Line number in the template source file.
     * @param string $tag
     */
    public function __construct($form, $lineno, $tag)
    {
        parent::__construct(array(), array(), $lineno, $tag);
        $this->form = $form;
    }

    /**
     * @param Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo \'<div class="row">\';')
            ->write('echo \'<div class="col-md-12">\';')
            ->write('echo \'<div class="panel panel-default">\';')
            ->write('echo \'<div class="panel-heading">\';')
            ->write('echo "<p class=\"tab-pane-title\">' . $this->lbl('Titles') . '</p>";')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="panel-body">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<ul class="list-unstyled checkboxTextFieldCombo">\';')
            ->write('echo \'<li class="checkbox">\';')
            ->write('echo "<label for=\"pageTitleOverwrite\" class=\"visuallyHidden\">";')
            ->write($this->getField('page_title_overwrite'))
            ->write('echo "<p>' . $this->lbl('PageTitle') . '</p></label>";')
            ->write($this->getError('page_title'))
            ->write($this->getField('page_title'))
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpPageTitle') . '</p>";')
            ->write('echo \'</li>\';')
            ->write('echo \'</ul>\';')
            ->write('echo \'</div>\';');

        $compiler
            ->write('if (' . $this->hasField('navigation_title_overwrite') . ') {')
            ->write('echo \'<div class="form-group last">\';')
            ->write('echo \'<ul class="list-unstyled checkboxTextFieldCombo">\';')
            ->write('echo \'<li class="checkbox">\';')
            ->write('echo "<label for=\"navigationTitleOverwrite\" class=\"visuallyHidden\">";')
            ->write($this->getField('navigation_title_overwrite'))
            ->write('echo "<p>' . $this->lbl('NavigationTitle') . '</p></label>";')
            ->write($this->getError('navigation_title'))
            ->write($this->getField('navigation_title'))
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpNavigationTitle') . '</p>";')
            ->write('echo \'</li>\';')
            ->write('echo \'</ul>\';')
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="row">\';')
            ->write('echo \'<div class="col-md-12">\';')
            ->write('echo \'<div class="panel panel-default">\';')
            ->write('echo \'<div class="panel-heading">\';')
            ->write('echo "<p class=\"tab-pane-title\">' . $this->lbl('MetaInformation') . '</p>";')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="panel-body">\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<ul class="list-unstyled checkboxTextFieldCombo">\';')
            ->write('echo \'<li class="checkbox">\';')
            ->write('echo "<label for=\"metaDescriptionOverwrite\" class=\"visuallyHidden\">";')
            ->write($this->getField('meta_description_overwrite'))
            ->write('echo "<p>' . $this->lbl('Description') . '</p></label>";')
            ->write($this->getError('meta_description'))
            ->write($this->getField('meta_description'))
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpMetaDescription') . '</p>";')
            ->write('echo \'</li>\';')
            ->write('echo \'</ul>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group">\';')
            ->write('echo \'<ul class="list-unstyled checkboxTextFieldCombo">\';')
            ->write('echo \'<li class="checkbox">\';')
            ->write('echo "<label for=\"metaKeywordsOverwrite\" class=\"visuallyHidden\">";')
            ->write($this->getField('meta_keywords_overwrite'))
            ->write('echo "<p>' . $this->lbl('Keywords') . '</p></label>";')
            ->write($this->getError('meta_keywords'))
            ->write($this->getField('meta_keywords'))
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpMetaKeywords') . '</p>";')
            ->write('echo \'</li>\';')
            ->write('echo \'</ul>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="form-group last">\';')
            ->write('echo "<label for=\"metaDescriptionOverwrite\" class=\"visuallyHidden\">' . $this->lbl('ExtraMetaTags') . '</label>";')
            ->write($this->getError('meta_custom'))
            ->write($this->getField('meta_custom'))
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpMetaCustom') . '</p>";')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="row">\';')
            ->write('echo \'<div class="col-md-12">\';')
            ->write('echo \'<div class="panel panel-default">\';')
            ->write('echo \'<div class="panel-heading">\';')
            ->write('echo "<p class=\"tab-pane-title\">' . $this->lbl('URL') . '</p>";')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="panel-body">\';')
            ->write('echo \'<div class="form-group last">\';')
            ->write('echo \'<ul class="list-unstyled checkboxTextFieldCombo">\';')
            ->write('echo \'<li class="checkbox">\';')
            ->write('echo "<label for=\"urlOverwrite\" class=\"visuallyHidden\">";')
            ->write($this->getField('url_overwrite'))
            ->write('echo "<p>' . $this->lbl('URL') . '</p></label>";')
            ->write('echo \'<div class="form-inline">\';');

        $compiler
            ->write('echo \'<span id="urlFirstPart">\';')
            ->write('if (' . $this->hasVariable('detailUrl') . ') {')
            ->write($this->getVariable('detailUrl'))
            ->write('} else {')
            ->write('echo "' . SITE_URL . '/' . ' ' . '";')
            ->write('}')
            ->write('echo \'</span>\';');

        $compiler
            ->write($this->getError('url'))
            ->write($this->getField('url'))
            ->write('echo \'</div>\';')
            ->write('echo "<p class=\"help-block\">' . $this->msg('HelpMetaURL') . '</p>";')
            ->write('echo \'</li>\';')
            ->write('echo \'</ul>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="row">\';')
            ->write('echo \'<div class="col-md-12">\';')
            ->write('echo \'<div class="panel panel-default">\';')
            ->write('echo \'<div class="panel-heading">\';')
            ->write('echo "<p class=\"tab-pane-title\">' . $this->lbl('SEO') . '</p>";')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="panel-body">\';')
            ->write('echo \'<div class="col-md-6">\';')
            ->write('echo \'<div class="form-inline">\';')
            ->write('echo \'<div class="form-group last">\';')
            ->write('echo "<p><b>' . $this->lbl('Index') . '</b></p>";');

        $compiler
            ->write('if (' . $this->hasError('seo_index') . ') {')
            ->write('echo \'<div class="alert alert-danger">\';')
            ->write($this->getError('seo_index'))
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'<ul class="list-unstyled inputListHorizontal">\';')
            ->write($this->loopTroughField('seo_index', '$index'))
            ->write('echo \'<li class="radio">\';')
            ->write('echo "<label for=\"" . $index["id"] . "\">" . $index["rbtSeoIndex"] . $index["label"] . "</label>";')
            ->write('echo \'</li>\';')
            ->write('}')
            ->write('echo \'</ul>\';');

        $compiler
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'<div class="col-md-6">\';')
            ->write('echo \'<div class="form-inline">\';')
            ->write('echo \'<div class="form-group last">\';')
            ->write('echo "<p><b>' . $this->lbl('Follow') . '</b></p>";');

        $compiler
            ->write('if (' . $this->hasError('seo_follow') . ') {')
            ->write('echo \'<div class="alert alert-danger">\';')
            ->write($this->getError('seo_follow'))
            ->write('echo \'</div>\';')
            ->write('}');

        $compiler
            ->write('echo \'<ul class="list-unstyled inputListHorizontal">\';')
            ->write($this->loopTroughField('seo_follow', '$follow'))
            ->write('echo \'<li class="radio">\';')
            ->write('echo "<label for=\"" . $follow["id"] . "\">" . $follow["rbtSeoFollow"] . $follow["label"] . "</label>";')
            ->write('echo \'</li>\';')
            ->write('}')
            ->write('echo \'</ul>\';');

        $compiler
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write('echo \'</div>\';')
            ->write($this->getField('meta_id'))
            ->write($this->getField('base_field_name'))
            ->write($this->getField('custom'))
            ->write($this->getField('class_name'))
            ->write($this->getField('method_name'))
            ->write($this->getField('parameters'))
            ->write('echo \'</div>\';');
    }

    private function lbl($label)
    {
        return ucfirst(BackendLanguage::getLabel($label));
    }

    private function msg($message)
    {
        return BackendLanguage::getMessage($message);
    }

    private function hasVariable($variable)
    {
        return "isset(\$context['{$variable}']) && !empty(\$context['{$variable}'])";
    }

    private function loopTroughField($variable, $as)
    {
        return "foreach (\$context['{$variable}'] as {$as}) {";
    }

    private function getVariable($variable)
    {
        return "echo \$context['{$variable}'];";
    }

    private function getField($fieldName)
    {
        $frm = "\$context['form_{$this->form}']";

        return 'echo ' . $frm . "->getField('" . $fieldName . "')->parse();";
    }

    private function hasField($fieldName)
    {
        $frm = "\$context['form_{$this->form}']";

        return $frm . "->existsField('" . $fieldName . "')";
    }

    private function hasError($fieldName)
    {
        return "\$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() ";
    }

    private function getError($fieldName)
    {
        return "echo \$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() "
            . "? '<span class=\"formError\">' "
            . ". \$context['form_{$this->form}']->getField('" . $fieldName . "')->getErrors() "
            . ". '</span>' : '';";
    }
}
