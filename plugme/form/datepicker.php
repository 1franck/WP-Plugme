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
        'format'      => 'yy-mm-dd', //@see jquery-ui datepicker doc
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