<?php
/**
 * Regex validator
 *
 * @see FILTER_VALIDATE_INT
 */
class plugme_form_validator_regex extends plugme_form_validator
{
    // no default options
    // regex expression goes directly in options vars
    
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        //$this->options
        return filter_var(
            $this->data, 
            FILTER_VALIDATE_REGEXP, 
            array('options' => array('regexp' => $this->options))
        );
    }
}