<?php
/**
 * Same as alpha but some default punctuations/symbol
 * ().?!-_,;'’"%$£:/#
 *
 * @param  string $v
 * @return bool
 */
include_once 'alpha.php';

class plugme_form_validator_text extends plugme_form_validator_alpha
{
    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        $this->options['space'] = true;
        $this->options['punc'] = array('(', ')', '.', '?', '!', '-',  '_', ',', ';', '\'', '’', '"', '%', '$', '£', ':', '/', '#');

        $regopt = $this->get_regex();
        $regopt[] = '0-9';

        return filter_var(
            $this->data, 
            FILTER_VALIDATE_REGEXP, 
            array('options' => array('regexp' => '/^['.implode('',$regopt).']+$/'))
        );        
    }
}