<?php

/**
 * Plugme admin utils class
 *
 * @author Francois Lajoie
 */
class plugme_admin
{

    protected $list_table; // instance of plugme_list_table
    protected $form;       // instance of plugme_form

    private $db;

    /**
     * object itself
     * @var object
     */
    private static $_instance = null; 
    
    /**
     * Singleton peak core
     *
     * @return  object instance
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Register a list table object
     * 
     * @param  object $object instance of plugme_list_table
     */
    public function register_list_table($object)
    {
        $this->list_table = $object;
    }

    /**
     * Register a form object
     * 
     * @param  object $object instance of plugme_form
     */
    public function register_form($object)
    {
        $this->form = $object;
    }

    private function has_registered_form()
    {
        if(!isset($this->form)) {
            wp_die('Plugme: you need to register a form first!');
        }
    }

    private function has_registered_list_table()
    {
        if(!isset($this->list_table)) {
            wp_die('Plugme: you need to register a list table first!');
        }
    }

    /**
     * Check if is creating a new item
     * 
     * @return boolean
     */
    public function is_creating_item()
    {
        if(isset($_GET['action']) && $_GET['action'] === 'new') {
            return true;
        }
        return false;
    }

    /**
     * User is trying to edit an item
     * 
     * @return boolean
     */
    public function is_editing_item()
    {
        $this->has_registered_list_table();

        if(isset($_GET[$this->list_table->get_option('singular')]) && isset($_GET['action']) && $_GET['action'] === 'edit') {
            return true;
        }
        return false;
    }


    /**
     * Get form action link for a new item
     * 
     * @return  string
     */
    public function get_new_item_link()
    {
        return '?page='.$_REQUEST['page'].'&action=new';
    }


    /**
     * Check if form submitted
     *
     * @param  array form data
     * @return boolean
     */
    public function is_form_submitted($data, $key = 'submit')
    {
        if(array_key_exists($key, $data)) return true;
        return false;
    }

    /**
     * Get user id item to edit if set
     * 
     * @return integer|null
     */
    public function get_editing_id()
    {
        if($this->is_editing_item()) {
            return $_GET[$this->list_table->get_option('singular')];
        }
    }

}