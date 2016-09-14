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
        'settings'    => array( // @see https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
            'teeny'         => true,
            'textarea_rows' => 14,
            'tabindex'      => 1,
            'media_buttons' => true,
            'wpautop'       => false,
            'editor_css'    => '',
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