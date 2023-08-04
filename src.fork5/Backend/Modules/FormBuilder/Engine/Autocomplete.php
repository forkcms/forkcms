<?php

namespace Backend\Modules\FormBuilder\Engine;

use Backend\Core\Language\Language;

/**
 * The autocomplete values can be found on
 *
 * https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
 */
final class Autocomplete
{
    const NAME = 'name';
    const HONORIFIC_PREFIX = 'honorific-prefix';
    const GIVEN_NAME = 'given-name';
    const ADDITIONAL_NAME = 'additional-name';
    const FAMILY_NAME = 'family-name';
    const NICKNAME = 'nickname';
    const EMAIL = 'email';
    const USERNAME = 'username';
    const NEW_PASSWORD = 'new-password';
    const CURRENT_PASSWORD = 'current-password';
    const ORGANIZATION_TITLE = 'organization-title';
    const ORGANIZATION = 'organization';
    const STREET_ADDRESS = 'street-address';
    const COUNTRY = 'country';
    const COUNTRY_NAME = 'country-name';
    const POSTAL_CODE = 'postal-code';
    const CC_NAME = 'cc-name';
    const CC_GIVEN_NAME = 'cc-given-name';
    const CC_ADDITIONAL_NAME = 'cc-additional-name';
    const CC_FAMILY_NAME = 'cc-family-name';
    const CC_NUMBER = 'cc-number';
    const CC_EXP = 'cc-exp';
    const CC_EXP_MONTH = 'cc-exp-month';
    const CC_EXP_YEAR = 'cc-exp-year';
    const CC_CSC = 'cc-csc';
    const CC_TYPE = 'cc-type';
    const TRANSACTION_CURRENCY = 'transaction-currency';
    const TRANSACTION_AMOUNT = 'transaction-amount';
    const LANGUAGE = 'language';
    const BDAY = 'bday';
    const BDAY_DAY = 'bday-day';
    const BDAY_MONTH = 'bday-month';
    const BDAY_YEAR = 'bday-year';
    const SEX = 'sex';
    const TEL = 'tel';
    const URL = 'url';
    const PHOTO = 'photo';

    const POSSIBLE_VALUES = [
        self::NAME,
        self::HONORIFIC_PREFIX,
        self::GIVEN_NAME,
        self::ADDITIONAL_NAME,
        self::FAMILY_NAME,
        self::NICKNAME,
        self::EMAIL,
        self::USERNAME,
        self::NEW_PASSWORD,
        self::CURRENT_PASSWORD,
        self::ORGANIZATION_TITLE,
        self::ORGANIZATION,
        self::STREET_ADDRESS,
        self::COUNTRY,
        self::COUNTRY_NAME,
        self::POSTAL_CODE,
        self::CC_NAME,
        self::CC_GIVEN_NAME,
        self::CC_ADDITIONAL_NAME,
        self::CC_FAMILY_NAME,
        self::CC_NUMBER,
        self::CC_EXP,
        self::CC_EXP_MONTH,
        self::CC_EXP_YEAR,
        self::CC_CSC,
        self::CC_TYPE,
        self::TRANSACTION_CURRENCY,
        self::TRANSACTION_AMOUNT,
        self::LANGUAGE,
        self::BDAY,
        self::BDAY_DAY,
        self::BDAY_MONTH,
        self::BDAY_YEAR,
        self::SEX,
        self::TEL,
        self::URL,
        self::PHOTO
    ];

    public static function getValuesForDropdown(): array
    {
        // map the values to replace them with the backend translations
        // use array combine to set the keys as the autocomplete values instead of key values
        return array_map(
            function (string $value): string {
                return $value . ' (' . Language::getLabel('Autocomplete_' . str_replace('-', '_', $value)) . ')';
            },
            array_combine(
                Autocomplete::POSSIBLE_VALUES,
                Autocomplete::POSSIBLE_VALUES
            )
        );
    }
}
