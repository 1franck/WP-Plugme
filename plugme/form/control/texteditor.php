<?php

/**
 * Text Editor (use wp_editor)
 */
class plugme_form_control_texteditor extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'       => '',
        'description' => '',  
        'attrs'       => array(
            'required'    => false,
            'placeholder' => '',
        ),
        'settings'    => array(
            'teeny'         => true,
            'textarea_rows' => 8,
            'tabindex'      => 1,
            'media_buttons' => true
        )
    );

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes();
        
        ob_start();
        wp_editor(stripslashes($this->data), $this->name, $this->options['settings']);
        $wp_editor = ob_get_contents();
        ob_end_clean();

        return $wp_editor;
    }
}