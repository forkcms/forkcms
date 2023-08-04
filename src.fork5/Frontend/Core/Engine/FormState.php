<?php

namespace Frontend\Core\Engine;

/**
 * Keeps the state of a form between opening and closing the tag.
 * Since forms cannot be nested, we can resort to a quick 'n dirty yes/no global state.
 *
 * In the future we could remove it and use stack {push,popp}ing, but I'm hoping
 * we're using symfony forms or some other more OO form library.
 */
class FormState
{
    public static $current;
}
