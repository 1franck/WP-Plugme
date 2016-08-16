<?php
/**
 * Plugme form control
 *
 * @author  Francois Lajoie
 */
abstract class plugme_form_control
{
    
    /**
     * Overload those props as you need
     */
    
    protected $default_options = array(
        'label'       => '',
        'description' => '',
        'attrs'       => array()
    );

    /**
     * Don't overload those props
     */
    protected $options = array();
    protected $name;
    protected $data;
    protected $assets_url;
    protected $attrs_array = array();
    
    /**
     * Define this method
     * 
     * @return string
     */
    abstract public function generate();

    /**
     * Optionnal
     */
    public function init() {}

    /**
     * Contructor
     * 
     * @param array $options 
     * @param mixed $data    
     */
    public function __construct($name, $data, $options)
    {
        $this->name = $name;

        if(!array_key_exists('attrs', $options)) {
            $options['attrs'] = array();
        }

        $this->options = $options;
        
        // merge attrs first
        $this->options['attrs'] = array_merge($this->default_options['attrs'], $this->options['attrs']);

        // merge the rest
        $this->options = array_merge($this->default_options, $this->options);

        $this->data = $data;

        $this->assets_url = plugins_url(basename(realpath(dirname(__FILE__).'/../')).'/plugme/assets');

        $this->init();
        // echo '<pre>';
        // print_r($this->options);

        //exit();
    }

    /**
     * Get all options
     * 
     * @return array
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * Get an option
     * 
     * @param  string $name 
     * @return mixed       
     */
    public function get_option($name)
    {
        return $this->options[$name];
    }

    /**
     * Generate html attributes from an array associativate
     * 
     * @param  array  $attrs
     * @param  bool   $data_as_attrs if true, data will be added to 'value' html attribute
     * @return string
     */
    protected function attributes($data_as_attrs = true) 
    {
        $attrs_array = array(
            'id'    => 'field-'.$this->name,
            'name'  => $this->name,
        );

        if($data_as_attrs) {
            $attrs_array['value'] = $this->data;
        }

        if(is_null($this->options['attrs'])) $this->options['attrs'] = array();
        $attrs_array = array_merge($attrs_array, $this->options['attrs']);

        //create a copy before transforming it
        $this->attrs_array = $attrs_array;

        //print_r($attrs_array);

        //special cases
        if(array_key_exists('multiple', $attrs_array) && $attrs_array['multiple'] === true) {
            $attrs_array['name'] .= '[]';
        }

        $attrs_string = array();
        //transform to html attribute string ( key="value" )
        foreach($attrs_array as $k => $v) {
            if(is_bool($v) && !is_integer($v)) {
                if($v === true) $attrs_string[] = $k;
                //echo $k.'BOOL ';
            }
            else $attrs_string[] = $k.'="'.$v.'"';
        }

        return implode(' ', $attrs_string);
    }

    /**
     * Check if string is json string
     * 
     * @param  string $string 
     * @return boolean         
     */
    protected function is_json($string) 
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}