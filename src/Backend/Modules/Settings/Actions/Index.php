<?php

namespace Backend\Modules\Settings\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use TijsVerkoyen\Akismet\Akismet;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Backend\Modules\Settings\Engine\Model as BackendSettingsModel;

/**
 * This is the index-action (default), it will display the setting-overview
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The form instance
     *
     * @var BackendForm
     */
    private $form;

    /**
     * Should we show boxes for their API keys
     *
     * @var bool
     */
    private $needsAkismet;
    private $needsGoogleMaps;
    private $needsGoogleRecaptcha;

    public function execute(): void
    {
        parent::execute();

        // get some data
        $modulesThatRequireAkismet = BackendExtensionsModel::getModulesThatRequireAkismet();
        $modulesThatRequireGoogleMaps = BackendExtensionsModel::getModulesThatRequireGoogleMaps();
        $modulesThatRequireGoogleRecaptcha = BackendExtensionsModel::getModulesThatRequireGoogleRecaptcha();

        // set properties
        $this->needsAkismet = (!empty($modulesThatRequireAkismet));
        $this->needsGoogleMaps = (!empty($modulesThatRequireGoogleMaps));
        $this->needsGoogleRecaptcha = !empty($modulesThatRequireGoogleRecaptcha);

        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        // list of default domains
        $defaultDomains = [str_replace(['http://', 'www.', 'https://'], '', SITE_URL)];

        // create form
        $this->form = new BackendForm('settingsIndex');

        // general settings
        $this->form->addText(
            'site_title',
            $this->get('fork.settings')->get('Core', 'site_title_' . BL::getWorkingLanguage(), SITE_DEFAULT_TITLE)
        );
        $this->form->addTextarea(
            'site_html_header',
            $this->get('fork.settings')->get('Core', 'site_html_header', null),
            'form-control code',
            'form-control danger code',
            true
        );
        $this->form->addTextarea(
            'site_start_of_body_scripts',
            $this->get('fork.settings')->get('Core', 'site_start_of_body_scripts', null),
            'form-control code',
            'form-control danger code',
            true
        );
        $this->form->addTextarea(
            'site_html_footer',
            $this->get('fork.settings')->get('Core', 'site_html_footer', null),
            'form-control code',
            'form-control danger code',
            true
        );
        $this->form->addTextarea(
            'site_domains',
            implode("\n", (array) $this->get('fork.settings')->get('Core', 'site_domains', $defaultDomains)),
            'form-control code',
            'form-control danger code'
        );

        // facebook settings
        $this->form->addText('facebook_admin_ids', $this->get('fork.settings')->get('Core', 'facebook_admin_ids', null));
        $this->form->addText('facebook_application_id', $this->get('fork.settings')->get('Core', 'facebook_app_id', null));
        $this->form->addText(
            'facebook_application_secret',
            $this->get('fork.settings')->get('Core', 'facebook_app_secret', null)
        );

        // twitter settings
        $this->form->addText(
            'twitter_site_name',
            ltrim($this->get('fork.settings')->get('Core', 'twitter_site_name', null), '@')
        );

        // ckfinder
        $this->form->addText(
            'ckfinder_license_name',
            $this->get('fork.settings')->get('Core', 'ckfinder_license_name', null)
        );
        $this->form->addText(
            'ckfinder_license_key',
            $this->get('fork.settings')->get('Core', 'ckfinder_license_key', null)
        );
        $this->form->addText(
            'ckfinder_image_max_width',
            $this->get('fork.settings')->get('Core', 'ckfinder_image_max_width', 1600)
        );
        $this->form->addText(
            'ckfinder_image_max_height',
            $this->get('fork.settings')->get('Core', 'ckfinder_image_max_height', 1200)
        );

        // date & time formats
        $this->form->addDropdown(
            'time_format',
            BackendModel::getTimeFormats(),
            $this->get('fork.settings')->get('Core', 'time_format')
        );
        $this->form->addDropdown(
            'date_format_short',
            BackendModel::getDateFormatsShort(),
            $this->get('fork.settings')->get('Core', 'date_format_short')
        );
        $this->form->addDropdown(
            'date_format_long',
            BackendModel::getDateFormatsLong(),
            $this->get('fork.settings')->get('Core', 'date_format_long')
        );

        // number formats
        $this->form->addDropdown(
            'number_format',
            BackendModel::getNumberFormats(),
            $this->get('fork.settings')->get('Core', 'number_format')
        );

        // create a list of the languages
        foreach ($this->get('fork.settings')->get('Core', 'languages', ['en']) as $abbreviation) {
            // is this the default language
            $defaultLanguage = $abbreviation === SITE_DEFAULT_LANGUAGE;

            // attributes
            $activeAttributes = [];
            $activeAttributes['id'] = 'active_language_' . $abbreviation;
            $redirectAttributes = [];
            $redirectAttributes['id'] = 'redirect_language_' . $abbreviation;

            // fetch label
            $label = BL::lbl(mb_strtoupper($abbreviation), 'Core');

            // default may not be unselected
            if ($defaultLanguage) {
                // add to attributes
                $activeAttributes['disabled'] = 'disabled';
                $redirectAttributes['disabled'] = 'disabled';

                // overrule in $_POST
                if (!isset($_POST['active_languages']) || !is_array($_POST['active_languages'])) {
                    $_POST['active_languages'] = [SITE_DEFAULT_LANGUAGE];
                } elseif (!in_array(
                    $abbreviation,
                    $_POST['active_languages']
                )
                ) {
                    $_POST['active_languages'][] = $abbreviation;
                }
                if (!isset($_POST['redirect_languages']) || !is_array($_POST['redirect_languages'])) {
                    $_POST['redirect_languages'] = [SITE_DEFAULT_LANGUAGE];
                } elseif (!in_array(
                    $abbreviation,
                    $_POST['redirect_languages']
                )
                ) {
                    $_POST['redirect_languages'][] = $abbreviation;
                }
            }

            // add to the list
            $activeLanguages = [
                [
                    'label' => $label,
                    'value' => $abbreviation,
                    'attributes' => $activeAttributes,
                    'variables' => ['default' => $defaultLanguage],
                ],
                $redirectLanguages[] = [
                    'label' => $label,
                    'value' => $abbreviation,
                    'attributes' => $redirectAttributes,
                    'variables' => ['default' => $defaultLanguage],
                ],
            ];
        }

        $hasMultipleLanguages = BackendModel::getContainer()->getParameter('site.multilanguage');

        // create multilanguage checkbox
        $this->form->addMultiCheckbox(
            'active_languages',
            $activeLanguages,
            $this->get('fork.settings')->get('Core', 'active_languages', [$hasMultipleLanguages])
        );
        $this->form->addMultiCheckbox(
            'redirect_languages',
            $redirectLanguages,
            $this->get('fork.settings')->get('Core', 'redirect_languages', [$hasMultipleLanguages])
        );

        // api keys are not required for every module
        if ($this->needsAkismet) {
            $this->form->addText(
                'akismet_key',
                $this->get('fork.settings')->get('Core', 'akismet_key', null)
            );
        }
        if ($this->needsGoogleMaps) {
            $this->form->addText(
                'google_maps_key',
                $this->get('fork.settings')->get('Core', 'google_maps_key', null)
            );
        }
        if ($this->needsGoogleRecaptcha) {
            $this->form->addText(
                'google_recaptcha_site_key',
                $this->get('fork.settings')->get('Core', 'google_recaptcha_site_key', null)
            );
            $this->form->addText(
                'google_recaptcha_secret_key',
                $this->get('fork.settings')->get('Core', 'google_recaptcha_secret_key', null)
            );
        }

        // cookies
        $this->form->addCheckbox('show_cookie_bar', $this->get('fork.settings')->get('Core', 'show_cookie_bar', false));
    }

    protected function parse(): void
    {
        parent::parse();

        // show options
        if ($this->needsAkismet) {
            $this->template->assign('needsAkismet', true);
        }
        if ($this->needsGoogleMaps) {
            $this->template->assign('needsGoogleMaps', true);
        }
        if ($this->needsGoogleRecaptcha) {
            $this->template->assign('needsGoogleRecaptcha', true);
        }

        // parse the form
        $this->form->parse($this->template);

        // parse the warnings
        $this->parseWarnings();
    }

    /**
     * Show the warnings based on the active modules & configured settings
     */
    private function parseWarnings(): void
    {
        // get warnings
        $warnings = BackendSettingsModel::getWarnings();

        // assign warnings
        $this->template->assign('warnings', $warnings);
    }

    private function validateForm(): void
    {
        // is the form submitted?
        if ($this->form->isSubmitted()) {
            // validate required fields
            $this->form->getField('site_title')->isFilled(BL::err('FieldIsRequired'));

            // date & time
            $this->form->getField('time_format')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('date_format_short')->isFilled(BL::err('FieldIsRequired'));
            $this->form->getField('date_format_long')->isFilled(BL::err('FieldIsRequired'));

            // number
            $this->form->getField('number_format')->isFilled(BL::err('FieldIsRequired'));

            // akismet key may be filled in
            if ($this->needsAkismet && $this->form->getField('akismet_key')->isFilled()) {
                // key has changed
                if ($this->form->getField('akismet_key')->getValue() != $this->get('fork.settings')->get('Core', 'akismet_key', null)) {
                    // create instance
                    $akismet = new Akismet($this->form->getField('akismet_key')->getValue(), SITE_URL);

                    // invalid key
                    if (!$akismet->verifyKey()) {
                        $this->form->getField('akismet_key')->setError(BL::err('InvalidAPIKey'));
                    }
                }
            }

            // domains filled in
            if ($this->form->getField('site_domains')->isFilled()) {
                // split on newlines
                $domains = explode("\n", trim($this->form->getField('site_domains')->getValue()));

                // loop domains
                foreach ($domains as $domain) {
                    // strip funky stuff
                    $domain = trim(str_replace(['www.', 'http://', 'https://'], '', $domain));

                    // invalid URL
                    if (!\SpoonFilter::isURL('http://' . $domain)) {
                        // set error
                        $this->form->getField('site_domains')->setError(BL::err('InvalidDomain'));

                        // stop looping domains
                        break;
                    }
                }
            }

            if ($this->form->getField('ckfinder_image_max_width')->isFilled()) {
                $this->form->getField(
                    'ckfinder_image_max_width'
                )->isInteger(BL::err('InvalidInteger'));
            }
            if ($this->form->getField('ckfinder_image_max_height')->isFilled()) {
                $this->form->getField(
                    'ckfinder_image_max_height'
                )->isInteger(BL::err('InvalidInteger'));
            }

            // no errors ?
            if ($this->form->isCorrect()) {
                // general settings
                $this->get('fork.settings')->set(
                    'Core',
                    'site_title_' . BL::getWorkingLanguage(),
                    $this->form->getField('site_title')->getValue()
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'site_html_header',
                    $this->form->getField('site_html_header')->getValue()
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'site_start_of_body_scripts',
                    $this->form->getField('site_start_of_body_scripts')->getValue()
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'site_html_footer',
                    $this->form->getField('site_html_footer')->getValue()
                );

                // facebook settings
                $this->get('fork.settings')->set(
                    'Core',
                    'facebook_admin_ids',
                    ($this->form->getField('facebook_admin_ids')->isFilled()) ? $this->form->getField(
                        'facebook_admin_ids'
                    )->getValue() : null
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'facebook_app_id',
                    ($this->form->getField('facebook_application_id')->isFilled()) ? $this->form->getField(
                        'facebook_application_id'
                    )->getValue() : null
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'facebook_app_secret',
                    ($this->form->getField('facebook_application_secret')->isFilled()) ? $this->form->getField(
                        'facebook_application_secret'
                    )->getValue() : null
                );

                // twitter settings
                /** @var \SpoonFormText $txtTwitterSiteName */
                $txtTwitterSiteName = $this->form->getField('twitter_site_name');
                if ($txtTwitterSiteName->isFilled()) {
                    $this->get('fork.settings')->set(
                        'Core',
                        'twitter_site_name',
                        '@' . ltrim($txtTwitterSiteName->getValue(), '@')
                    );
                }

                // ckfinder settings
                $this->get('fork.settings')->set(
                    'Core',
                    'ckfinder_license_name',
                    ($this->form->getField('ckfinder_license_name')->isFilled()) ? $this->form->getField(
                        'ckfinder_license_name'
                    )->getValue() : null
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'ckfinder_license_key',
                    ($this->form->getField('ckfinder_license_key')->isFilled()) ? $this->form->getField(
                        'ckfinder_license_key'
                    )->getValue() : null
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'ckfinder_image_max_width',
                    ($this->form->getField('ckfinder_image_max_width')->isFilled()) ? $this->form->getField(
                        'ckfinder_image_max_width'
                    )->getValue() : 1600
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'ckfinder_image_max_height',
                    ($this->form->getField('ckfinder_image_max_height')->isFilled()) ? $this->form->getField(
                        'ckfinder_image_max_height'
                    )->getValue() : 1200
                );

                // api keys
                if ($this->needsAkismet) {
                    $this->get('fork.settings')->set(
                        'Core',
                        'akismet_key',
                        $this->form->getField('akismet_key')->getValue()
                    );
                }
                if ($this->needsGoogleMaps) {
                    $this->get('fork.settings')->set(
                        'Core',
                        'google_maps_key',
                        $this->form->getField('google_maps_key')->getValue()
                    );
                }
                if ($this->needsGoogleRecaptcha) {
                    $this->get('fork.settings')->set(
                        'Core',
                        'google_recaptcha_site_key',
                        $this->form->getField('google_recaptcha_site_key')->getValue()
                    );
                    $this->get('fork.settings')->set(
                        'Core',
                        'google_recaptcha_secret_key',
                        $this->form->getField('google_recaptcha_secret_key')->getValue()
                    );
                }

                // date & time formats
                $this->get('fork.settings')->set('Core', 'time_format', $this->form->getField('time_format')->getValue());
                $this->get('fork.settings')->set(
                    'Core',
                    'date_format_short',
                    $this->form->getField('date_format_short')->getValue()
                );
                $this->get('fork.settings')->set(
                    'Core',
                    'date_format_long',
                    $this->form->getField('date_format_long')->getValue()
                );

                // date & time formats
                $this->get('fork.settings')->set(
                    'Core',
                    'number_format',
                    $this->form->getField('number_format')->getValue()
                );

                // before we save the languages, we need to ensure that each language actually exists and may be chosen.
                $languages = [SITE_DEFAULT_LANGUAGE];
                $activeLanguages = array_unique(
                    array_merge($languages, $this->form->getField('active_languages')->getValue())
                );
                $redirectLanguages = array_unique(
                    array_merge($languages, $this->form->getField('redirect_languages')->getValue())
                );

                // cleanup redirect-languages, by removing the values that aren't present in the active languages
                $redirectLanguages = array_intersect($redirectLanguages, $activeLanguages);

                // save active languages
                $this->get('fork.settings')->set('Core', 'active_languages', $activeLanguages);
                $this->get('fork.settings')->set('Core', 'redirect_languages', $redirectLanguages);

                // domains may not contain www, http or https. Therefor we must loop and create the list of domains.
                $siteDomains = [];

                // domains filled in
                if ($this->form->getField('site_domains')->isFilled()) {
                    // split on newlines
                    $domains = explode("\n", trim($this->form->getField('site_domains')->getValue()));

                    // loop domains
                    foreach ($domains as $domain) {
                        // strip funky stuff
                        $siteDomains[] = trim(str_replace(['www.', 'http://', 'https://'], '', $domain));
                    }
                }

                // save domains
                $this->get('fork.settings')->set('Core', 'site_domains', $siteDomains);

                $this->get('fork.settings')->set(
                    'Core',
                    'show_cookie_bar',
                    $this->form->getField('show_cookie_bar')->getChecked()
                );

                // assign report
                $this->template->assign('report', true);
                $this->template->assign('reportMessage', BL::msg('Saved'));
            }
        }
    }
}
