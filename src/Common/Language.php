<?php

namespace Common;

use Common\Core\Model;
use Frontend\Core\Language\Language as FrontendLanguage;
use InvalidArgumentException;
use Symfony\Component\Translation\IdentityTranslator;

/**
 * This class will make it possible to have 1 function to get the correct language class.
 * This is useful for when you want to use the same code in the Back- and Frontend.
 * For instance in a trait.
 *
 * @TODO switch our translation system to completely work with symfony instead of overriding methods to make it run
 * on our system
 */
final class Language extends IdentityTranslator
{
    public static function get(): string
    {
        $application = 'Backend';

        if (Model::requestIsAvailable()
            && Model::getRequest()->attributes->has('_route')
            && stripos(Model::getRequest()->attributes->get('_route'), 'frontend') === 0
        ) {
            $application = 'Frontend';
        }

        return $application . '\Core\Language\Language';
    }

    /**
     * @param string $function
     * @param array $parameters
     *
     * @throws InvalidArgumentException when the function can't be called
     *
     * @return mixed
     */
    public static function callLanguageFunction(string $function, array $parameters = [])
    {
        $languageClass = self::get();
        $callback = [$languageClass, $function];
        if (!method_exists($languageClass, $function) || !is_callable($callback)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The function %s::%s does not exist',
                    $languageClass,
                    $function
                )
            );
        }

        return call_user_func_array($callback, $parameters);
    }

    /**
     * Get a label.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param string $key
     *
     * @return string
     */
    public static function lbl(string $key): string
    {
        return self::callLanguageFunction('lbl', [$key]);
    }

    /**
     * Get an error.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param string $key
     *
     * @return string
     */
    public static function err(string $key): string
    {
        return self::callLanguageFunction('err', [$key]);
    }

    /**
     * Get an action.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException when used in the backend.
     *
     * @return string
     */
    public static function act(string $key): string
    {
        return self::callLanguageFunction('act', [$key]);
    }

    /**
     * Get a message.
     * This only implements the key because the other parameters differ between the front- and backend.
     *
     * @param string $key
     *
     * @return string
     */
    public static function msg(string $key): string
    {
        return self::callLanguageFunction('msg', [$key]);
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        $possibleActions = ['lbl', 'err', 'msg'];
        if (self::get() === FrontendLanguage::class) {
            $possibleActions[] = 'act';
        }

        if (!preg_match('/(' . implode('|', $possibleActions) . ')./', $id)) {
            return parent::trans($id, $parameters, $domain, $locale);
        }

        if (!strpos($id, '.')) {
            return parent::trans($id, $parameters, $domain, $locale);
        }

        list($action, $string) = explode('.', $id, 2);

        if (!in_array($action, $possibleActions)) {
            return parent::trans($id, $parameters, $domain, $locale);
        }

        $translatedString = self::$action($string);

        // we couldn't translate it, let the parent have a go
        if (preg_match('/\{\$' . $action . '.*' . $string . '\}/', $translatedString)) {
            $parentTranslatedString = parent::trans($id, $parameters, $domain, $locale);
            // If the parent couldn't translate return our default
            if ($id === $parentTranslatedString) {
                return $translatedString;
            }

            return $parentTranslatedString;
        }

        return $translatedString;
    }
}
