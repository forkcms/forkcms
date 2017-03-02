<?php

namespace Common\Core;

/**
 * This is our extended version of \SpoonFormCheckbox
 */
class CommonFormCheckbox extends \SpoonFormCheckbox
{
    /**
     * Returns the value corresponding with the state of the checkbox
     *
     * @param mixed $checked the return value when checked
     * @param mixed $notChecked the return value when not checked
     *
     * @return mixed
     */
    public function getActualValue($checked = 'Y', $notChecked = 'N')
    {
        return $this->isChecked() ? $checked : $notChecked;
    }
}
