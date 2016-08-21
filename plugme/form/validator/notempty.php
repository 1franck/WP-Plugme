<?php
/**
 * Not empty validator
 */
class plugme_form_validator_notempty extends plugme_form_validator
{
    // no default options
    
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        return empty($this->data);
    }
}