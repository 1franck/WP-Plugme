<?php
/**
 * Alpha num validator (extends alpha validator)
 */

include_once 'alpha.php';

class plugme_form_validator_alphanum extends plugme_form_validator_alpha
{
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        $regopt = $this->get_regex();

        $regopt[] = '0-9';

        return filter_var(
            $this->data, 
            FILTER_VALIDATE_REGEXP, 
            array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/'))
        );        
    }
}