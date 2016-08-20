<?php
/**
 * Integer validator
 *
 * @see FILTER_VALIDATE_INT
 */
class plugme_form_validator_int extends plugme_form_validator
{
    /**
     * Default validator options
     * @var array
     */
    public $default_options = array(
        'min' => null,
        'max' => null,
    );

    /**
     * Validate data
     * 
     * @return bool
     */
    public function validate()
    {
        if(!isset($this->options)) {
            return filter_var($this->data, FILTER_VALIDATE_INT);
        }
        else {
            if(filter_var($this->data, FILTER_VALIDATE_INT) !== false) {
                $return = array();
                if(!is_null($this->options['min'])) {
                    $return[] = ($this->data >= $this->options['min']) ? true : false;
                }
                if(!is_null($this->options['max'])) {
                    $return[] = ($this->data <= $this->options['max']) ? true : false;
                }
                foreach($return as $r) if($r === false) return false;
                return true;
            }
            else return false;
        }
    }
}