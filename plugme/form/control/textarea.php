<?php

/**
 * HTML Textarea 
 */
class plugme_form_control_textarea extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'       => '',
        'description' => '',  
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
            'row'         => 3,
            'class'       => 'large-text',
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
        return '<textarea '.$attrs.'>'.$this->data.'</textarea><br>';
    }
}