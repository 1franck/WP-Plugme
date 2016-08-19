<?php
/**
 * Plugme form validation
 *
 * @author  Francois Lajoie
 */
class plugme_form_validation
{
    protected $validators;
    protected $data;

    public function __construct($validators, $data)
    {
        $this->validators = $validators;
        $this->data = $data;
    }

    public function validate($data)
    {
        if(!empty($validator) && !empty($data)) {

            foreach($validator as $fieldname => $v) {

                if(array_key_exists('validation', $v)) {

                    foreach($v['validatin'] as $validator => $vdata) {
                        
                    }
                }
                else {
                    echo 'No validation found for '.$k
                }
            }
        }
    }
}