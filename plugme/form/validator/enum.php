<?php
/**
 * Enum validator
 *
 * @see FILTER_VALIDATE_INT
 */
class plugme_form_validator_enum extends plugme_form_validator
{
    //no default options
    
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        return in_array($this->data, $this->options);
    }
}