<?php

/**
 * Select chosen (use jquery chosen)
 */
class plugme_form_control_chosen extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description' => '', 
        'options'     => array(),
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
            'multiple'    => false,
        ),
    );

    /**
     * Init jquery chosen
     */
    public function init()
    {
        if(!wp_script_is( 'plugme-chosen', 'enqueued' )) {
            wp_register_script("plugme-chosen", $this->assets_url.'/chosen/chosen.jquery.min.js');
            wp_register_style("plugme-chosen", $this->assets_url.'/chosen/chosen.min.css');
            wp_enqueue_script('plugme-chosen', time(), true);
            wp_enqueue_style( 'plugme-chosen', time(), true);
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

        if($this->is_json($this->data)) {
            $this->data = json_decode($this->data);
        }

        $control = '<select '.$attrs.' class="chosen-select">';
        foreach($this->options['options'] as $k => $v) {
            $selected = '';
            if(is_array($this->data) && in_array($k, $this->data)) {
                $selected = 'selected';
            }
            elseif($k == $this->data) $selected = 'selected';

            $control .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        $control .= '</select><br>';

        $control .= '
            <script>
                jQuery(document).ready(function(){
                    jQuery("#'.$this->attrs_array['id'].'").chosen({});
                });
            </script>';

        return $control;
    }
}