<?php

/**
 * HTML Datepicker (use jquery-ui)
 */
class plugme_form_control_datepicker extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'       => '',
        'format'      => 'yy-mm-dd', //@see jquery-ui datepicker doc
        'description' => '',  
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
        ),
    );

    /**
     * Init jquery ui
     */
    public function init()
    {
        if(!wp_script_is( 'jquery-ui-core', 'enqueued' )) {
            wp_enqueue_script('jquery-ui-core', time(), true);
        }
        if(!wp_script_is( 'jquery-ui-datepicker', 'enqueued' )) {
            wp_enqueue_script('jquery-ui-datepicker', time(), true);
        }
    }

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes();

        $control = '
            <input type="text" '.$attrs.' />
            <span class="dashicons dashicons-calendar-alt" style="margin-left:-30px;margin-top:5px;pointer-events: none;"></span><br>
            <script>
                jQuery(document).ready(function(){
                    jQuery("#'.$this->attrs_array['id'].'").datepicker({ dateFormat: "'.$this->options['format'].'" }); 
                });
            </script>';

        return $control;
    }
}