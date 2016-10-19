<?php

/**
 * HTML Select
 */
class plugme_form_control_select extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'             => '',
        'description'       => '', 
        'options'           => array(),
        'value_as_key'      => false,
        'attrs'       => array(
            'required'    => false,
            'placeholder' => '',
            'multiple'    => false,
        ),
    );

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes(false);

        $control = '<select '.$attrs.'>';

        if(array_key_exists('options', $this->options) && !empty($this->options['options'])) {
            foreach($this->options['options'] as $k => $v) {
                if($this->options['value_as_key']) $k = $v;
                $control .= '<option '.(($k == $this->data) ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
            }
        }
        
        $control .= '</select><br>';

        return $control;
    }
}