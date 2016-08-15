<?php

/**
 * HTML Text Input 
 */
class plugme_form_control_text extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description' => '',  
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
        ),
    );

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes();
        return '<input class="regular-text" type="text" '.$attrs.'><br>';
    }
}