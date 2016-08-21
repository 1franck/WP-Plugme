<?php
/**
 * Plugme form validation
 *
 * @author  Francois Lajoie
 */

include 'plugme.form.validator.php';

class plugme_form_validation
{
    protected $validators = array();
    protected $data       = array();
    protected $errors     = array();

    /**
     * Validate data
     * Errors are stocked $errors
     * 
     * @param  $validators validator field and stuff
     * @param  $data       data to validate
     * @return bool        validation result
     */
    public function validate($validators, $data)
    {
        
        $this->validators = $validators;
        $this->data = $data;


        if(!empty($this->validators) && !empty($this->data)) {

            foreach($this->validators as $fieldname => $v) {

                // has validation stuff
                if(array_key_exists('validation', $v)) {

                    // process validator
                    foreach($v['validation'] as $validator => $vdata) {

                        //conditional validator
                        if(isset($skip_validation_for) && $skip_validation_for === $fieldname) continue;
                        else $skip_validation_for = null;

                        if($validator == 0 && $vdata === 'if_not_empty') {
                            if(empty($this->data[$fieldname])) {
                                $skip_validation_for = $fieldname;
                                continue;
                            }
                        }

                        //check validation config
                        $error_msg = __('An error occurred'); //default validator error msg
                        $options   = array(); // default validator option(s)
                        
                        if(is_array($vdata)) {

                            // check for custom validator error msg
                            if(array_key_exists('msg', $vdata)) {
                                $error_msg = __($vdata['msg']);
                            }

                            // check for custom validator option(s)
                            if(array_key_exists('options', $vdata)) {
                                $options = __($vdata['options']);
                            }
                        }
                        else $error_msg = __($vdata['msg']);


                        // process the validator
                        $validator_cn = 'plugme_form_validator_'.$validator;
                        if(!class_exists($validator_cn, false)) {
                            $form_validator_file = dirname(__FILE__).'/form/validator/'.$validator.'.php';
                            if(!file_exists($form_validator_file)) {
                                if(!WP_DEBUG) continue;
                                else wp_die(__CLASS__.': validator type "'.$validator.'" not found');
                            }
                            else {
                                include $form_validator_file;
                                if(!class_exists($validator_cn, false)) {
                                    if(!WP_DEBUG) continue;
                                    else wp_die(__CLASS__.': validator type class "'.$validator_cn.'" not found in '.basename($form_validator_file));
                                }
                            }                   
                        }

                        if(array_key_exists($fieldname, $this->data)) {

                            $validator_inst = new $validator_cn($fieldname, $this->data[$fieldname], $options);

                            $pass = $validator_inst->validate();

                            //echo $fieldname.' '.$this->data[$fieldname].' '.print_r($options, true).'<br>';

                            if($pass === false) {
                                $this->errors[$fieldname] = $error_msg;
                            }
                        }
                        else {
                            //no data to check for the current validator
                        }

                        //
                        
                    }
                }
                else if(WP_DEBUG) {
                    //echo 'No validation found for control "'.$fieldname.'"';
                }
            }
        }

        return (empty($this->errors)) ? true : false;
    }

    public function get_errors()
    {
        return $this->errors;
    }
}