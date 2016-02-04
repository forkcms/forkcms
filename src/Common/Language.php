<?php

namespace Common;

/**
 * This class will make it possible to have 1 function to get the correct language class.
 * This is useful for when you want to use the same code in the Back- and Frontend.
 * For instance in a trait.
 *
 * @author Jelmer Prins <jelmer@$sumocoders.be>
 */
class Language
{
    public static function get()
    {
        return APPLICATION . '\Core\Engine\Language';
    }
}
