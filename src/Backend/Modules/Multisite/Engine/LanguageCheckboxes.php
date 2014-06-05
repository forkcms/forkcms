<?php

namespace Backend\Modules\Multisite\Engine;

use Backend\Core\Engine\Language;

/**
 * Helper for building the multisite forms.
 * Especially re. languages and whether they are active or not, because these
 * are rather cumbersome to create in Spoon/Fork, and we don't want to repeat
 * the hassle between add and edit actions.
 *
 * @author <per@wijs.be>
 * @author Wouter Sioen <wouter@wijs.be>
 */
class LanguageCheckboxes
{
    /**
     * @param BackendForm $frm The form to add all the language checkboxes to.
     * @param array[optional] $languages The languages that are already checked,
     *        this includes the is_active/viewable values.
     * @return array List of parsed multicheckboxes that can be iterated over
     *         easily for display in a template.
     */
    public static function addToForm($frm, $siteLanguages = array())
    {
        $languageCheckboxValues = Language::getCheckboxValues();
        $languageActiveCheckboxValues = array();
        $languageViewableCheckboxValues = array();
        $languageChecked = array();
        $languageActiveChecked = array();
        $languageViewableChecked = array();
        foreach ($languageCheckboxValues as $cbv) {
            $language = $cbv['value'];
            $languageActiveCheckboxValues[] = array(
                'label' => Language::lbl('Active'),
                'value' => $language . '_active',
            );
            $languageViewableCheckboxValues[] = array(
                'label' => Language::lbl('Viewable'),
                'value' => $language . '_viewable',
            );
            foreach ($siteLanguages as $siteLanguage) {
                $languageChecked[] = $siteLanguage['language'];
                if ($language == $siteLanguage['language']) {
                    if ($siteLanguage['is_active'] == 'Y') {
                        $languageActiveChecked[] = $language . '_active';
                    } if ($siteLanguage['is_viewable'] == 'Y') {
                        $languageViewableChecked[] = $language . '_viewable';
                    }
                }
            }
        }
        $chkLanguages = $frm->addMultiCheckbox(
            'languages',
            Language::getCheckboxValues(),
            $languageChecked
        )->parse();
        $chkLanguagesActive = $frm->addMultiCheckbox(
            'languages_active',
            $languageActiveCheckboxValues,
            $languageActiveChecked
        )->parse();
        $chkLanguagesViewable = $frm->addMultiCheckbox(
            'languages_viewable',
            $languageViewableCheckboxValues,
            $languageViewableChecked
        )->parse();
        $tplFriendly = array();
        for ($i = 0; $i < count($chkLanguages); $i++) {
            $tplFriendly[] = array(
                'language' => $chkLanguages[$i]['element'],
                'languageLabel' => $chkLanguages[$i]['label'],
                'languageId' => $chkLanguages[$i]['id'],
                'active' => $chkLanguagesActive[$i]['element'],
                'activeLabel' => ucfirst($chkLanguagesActive[$i]['label']),
                'activeId' => $chkLanguagesActive[$i]['id'],
                'viewable' => $chkLanguagesViewable[$i]['element'],
                'viewableLabel' => ucfirst($chkLanguagesViewable[$i]['label']),
                'viewableId' => $chkLanguagesViewable[$i]['id'],
            );
        }

        return $tplFriendly;
    }

    /**
     * @param BackendForm $frm The form to get the language values from
     * @return array Language values in a structured that can be processed by
     *         BackendMultisiteModel.
     */
    public static function getValues($frm)
    {
        $languages = $frm->getField('languages')->getChecked();
        $languagesActive = $frm->getField('languages_active')->getChecked();
        $languagesViewable = $frm->getField('languages_viewable')->getChecked();
        $formatted = array();
        foreach ($languages as $language) {
            $formatted[] = array(
                'language' => $language,
                'is_active' => in_array($language . '_active', $languagesActive)
                    ? 'Y' : 'N',
                'is_viewable' => in_array($language . '_viewable', $languagesViewable)
                    ? 'Y' : 'N',
            );
        }

        return $formatted;
    }
}
