<?php

/**
 * HTML Select
 */
class plugme_form_control_radio extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description' => '', 
        'options'     => array(),
        'inline'      => false,
        'attrs'       => array(
            'required'    => false,
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

        $control = '
            <fieldset>
                <legend class="screen-reader-text">
                    <span>'.$this->options['label'].'</span>
                </legend>';


        if(array_key_exists('options', $this->options) && !empty($this->options['options'])) {
            foreach($this->options['options'] as $k => $v) {
                $control .= '
                    <label>
                        <input type="radio" '.(($k == $this->data) ? 'checked' : '').' value="'.$k.'" name="'.$this->name.'">
                        <span>'.$v.'</span>
                    </label>';

                if(!$this->options['inline']) $control .= '<br>';
            }
        }
        
        $control .= '</fieldset><br>';

        return $control;
    }
}