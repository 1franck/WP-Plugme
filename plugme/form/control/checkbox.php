<?php

/**
 * HTML Checkbox 
 */
class plugme_form_control_checkbox extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description'    => '',
        'checkbox_label' => '',
        'attrs' => array(
            'required'    => false,
            'value'       => 1
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
        return '
            <label for="'.$this->attrs_array['id'].'">
                <input type="checkbox" '.$attrs.' value="'.$this->options['attrs']['value'].'">
                '.$this->options['checkbox_label'].'
            </label>';
    }
}