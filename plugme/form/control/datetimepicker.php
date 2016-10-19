<?php

/**
 * HTML Datepicker (@see http://xdsoft.net/jqplugins/datetimepicker/)
 */
class plugme_form_control_datetimepicker extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'       => '',
        'format'      => 'Y-m-d H:i',
        'description' => '',
        'datepicker'  => true,
        'timepicker'  => true,
        'weeks'       => true,
        'mask'        => true,
        'lang'        => 'en',
        'step'        => 60,    //for time
        'todayButton' => true,
        'defaultDate' => false,
        'defaultTime' => false,
        'allowTimes'  => [],
        'allowDates'  => [],
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
        ),
    );

    /**
     * Init the plugins
     */
    public function init()
    {
        if(!wp_script_is( 'plugme-jquerydatetimepicker', 'enqueued' )) {
            wp_register_script("plugme-jquerydatetimepicker", $this->assets_url.'/jquerydatetimepicker/jquery.datetimepicker.full.min.js');
            wp_register_style("plugme-jquerydatetimepicker", $this->assets_url.'/jquerydatetimepicker/jquery.datetimepicker.min.css');
            wp_enqueue_script('plugme-jquerydatetimepicker', time(), true);
            wp_enqueue_style( 'plugme-jquerydatetimepicker', time(), true);
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
            <input type="text" '.$attrs.' style="min-width:175px;" />
            <span class="dashicons dashicons-calendar-alt" style="margin-left:-30px;margin-top:5px;pointer-events: none;"></span><br>
            <script>
                jQuery(document).ready(function(){
                    jQuery.datetimepicker.setLocale("'.$this->options['lang'].'");
                    jQuery("#'.$this->attrs_array['id'].'").datetimepicker({ 
                        format: "'.$this->options['format'].'", 
                        datepicker: '.$this->bool2String($this->options['datepicker']).', 
                        timepicker: '.$this->bool2String($this->options['timepicker']).', 
                        weeks: '.$this->bool2String($this->options['weeks']).',  
                        mask: '.$this->bool2String($this->options['mask']).',  
                        step: "'.$this->options['step'].'", 
                        todayButton: '.$this->bool2String($this->options['todayButton']).',
                        defaultDate: "'.$this->options['defaultDate'].'", 
                        defaultTime: "'.$this->options['defaultTime'].'", 
                        allowTimes: '.$this->array2JsArray($this->options['allowTimes']).', 
                        allowDates: '.$this->array2JsArray($this->options['allowDates']).',
                    }); 
                });
            </script>';

        return $control;
    }


}