<?php
/**
 * Plugme form validator
 *
 * @author  Francois Lajoie
 */
abstract class plugme_form_validator
{
    
    /**
     * Overload those props as you need
     */
    protected $default_options = array();

    /**
     * Don't overload those props
     */
    protected $options = array();
    protected $name;
    protected $data;
    
    /**
     * Define this method
     * 
     * @return bool
     */
    abstract public function validate();

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

        if(is_array($options)){
            $this->options = array_merge($this->default_options, $options);
        }
        else $this->options = $options;

        $this->data = $data;

        $this->init();
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
     * Shortcut of get_options()
     *
     * Beware that for php < 5.5, php will throw a fatal error if you use this method in a condition statement
     * (A Fatal Error is being occurring in if condition a method is returning value)
     */
    public function o($name)
    {
        return $this->get_option($name);
    }
}