<?php

/**
 * HTML Input (default type is text)
 */
class plugme_form_control_input extends plugme_form_control
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
            'class'       => 'regular-text',
            'spellcheck'  => 'true',
            'type'        => 'text',
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
        return '<input '.$attrs.'><br>';
    }
}