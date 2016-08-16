<?php

/**
 * CSS Switch toggle (HTML Checkbox behind)
 */
class plugme_form_control_switch extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'description'    => '', 
        'checkbox_label' => '', 
        'attrs'       => array(
            'required' => false,
            'value'    => 1
        ),
    );

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes(FALSE);
        return '
            <label for="'.$this->attrs_array['id'].'">
                <div class="onoffswitch">
                    <input type="checkbox" class="onoffswitch-checkbox" value="'.$this->options['attrs']['value'].'" '.(($this->data == 1) ? 'checked' : '').' '.$attrs.'>
                    <label class="onoffswitch-label" for="field-'.$this->name.'"></label>
                </div>
                <span>'.$this->options['checkbox_label'].'</span>
            </label>';
    }
}