<?php

/**
 * Slider (use jquery-ui)
 */
class plugme_form_control_slider extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description' => '',
        'attrs'       => array(),
        'settings'    => array( // @see http://api.jqueryui.com/slider/ for more
            'animate'     => false,
            'max'         => 100,
            'min'         => 0,
            'step'        => 1,
            'range'       => false,
            'orientation' => 'horizontal'
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

        $this->options['settings']['value'] = $this->data;

        $control = '
            <div id="slider-'.$this->attrs_array['id'].'" style="max-width:400px;"></div>
            <input type="hidden" '.$attrs.' />
            <script>
                jQuery(document).ready(function(){
                    jQuery("#slider-'.$this->attrs_array['id'].'").slider('.json_encode($this->options['settings']).'); 
                });
            </script>';

        return $control;
    }
}