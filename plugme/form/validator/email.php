<?php
/**
 * Email validator
 *
 * @see FILTER_VALIDATE_EMAIL
 */
class plugme_form_validator_email extends plugme_form_validator
{
    // no default options
    
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        return filter_var(
            $this->data, 
            FILTER_VALIDATE_EMAIL
        );
    }
}