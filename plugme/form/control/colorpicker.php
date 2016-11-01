<?php

/**
 * jQuery Colorpicker (@see https://bgrins.github.io/spectrum/)
 */
class plugme_form_control_colorpicker extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'                 => '',
        'description'           => '',

        'flat'                  => false,
        'showInput'             => true,
        'showInitial'           => true,
        'showButtons'           => true,
        'allowEmpty'            => true,
        'showAlpha'             => false,
        'disabled'              => false,
        'showPalette'           => false,
        'showPaletteOnly'       => false,
        'togglePaletteOnly'     => false,
        'showSelectionPalette'  => false,
        'clickoutFiresChange'   => true,
        'cancelText'            => 'Cancel',
        'chooseText'            => 'Select',
        'togglePaletteMoreText' => 'More',
        'togglePaletteLessText' => 'Less',
        'preferredFormat'       => 'hex', // hex, hsl, rgb
        'maxSelectionSize'      => 10,
        'palette'               => array("001f3f", "0074D9", "7FDBFF","39CCCC","3D9970","2ECC40","01FF70","FFDC00","FF851B","FF4136","85144b","F012BE","B10DC9","111111","AAAAAA","DDDDDD"),
        'selectionPalette'      => array(),

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
        if(!wp_script_is( 'plugme-spectrum', 'enqueued' )) {
            wp_register_script("plugme-spectrum", $this->assets_url.'/spectrum/spectrum.js');
            wp_register_style("plugme-spectrum", $this->assets_url.'/spectrum/spectrum.css');
            wp_enqueue_script('plugme-spectrum', time(), true);
            wp_enqueue_style( 'plugme-spectrum', time(), true);
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
            
            <input type="text" '.$attrs.' style="min-width:175px;" readonly />
            <script>
                jQuery(document).ready(function(){
                    jQuery("#'.$this->attrs_array['id'].'").spectrum({
                        "flat"                  : '.$this->bool2String($this->options['flat']).',
                        "showInput"             : '.$this->bool2String($this->options['showInput']).',
                        "showInitial"           : '.$this->bool2String($this->options['showInitial']).',
                        "showButtons"           : '.$this->bool2String($this->options['showButtons']).',
                        "allowEmpty"            : '.$this->bool2String($this->options['showInput']).',
                        "showAlpha"             : '.$this->bool2String($this->options['showAlpha']).',
                        "disabled"              : '.$this->bool2String($this->options['disabled']).',
                        //"localStorageKey"       : "spectrum.'.$_GET['page'].'",
                        "showPalette"           : '.$this->bool2String($this->options['showPalette']).',
                        "showPaletteOnly"       : '.$this->bool2String($this->options['showPaletteOnly']).',
                        "togglePaletteOnly"     : '.$this->bool2String($this->options['togglePaletteOnly']).',
                        "showSelectionPalette"  : '.$this->bool2String($this->options['showSelectionPalette']).',
                        "clickoutFiresChange"   : '.$this->bool2String($this->options['clickoutFiresChange']).',
                        "cancelText"            : "'.__($this->options['cancelText']).'",
                        "chooseText"            : "'.__($this->options['chooseText']).'",
                        "togglePaletteMoreText" : "'.__($this->options['togglePaletteMoreText']).'",
                        "togglePaletteLessText" : "'.__($this->options['togglePaletteLessText']).'",
                        "preferredFormat"       : "'.$this->options['preferredFormat'].'",
                        "maxSelectionSize"      : '.$this->options['maxSelectionSize'].',
                        "palette"               : '.$this->array2JsArray($this->options['palette']).',
                        "selectionPalette"      : '.$this->array2JsArray($this->options['selectionPalette']).',
                        change: function(color) {
                            jQuery("#'.$this->attrs_array['id'].'").val(color.toHexString());
                        }
                    });
                });
            </script>';

        return $control;
    }


}