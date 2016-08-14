<?php

/**
 * Plugme Data Source
 *
 * @author  Francois Lajoie
 */
abstract class plugme_data_source
{

    /**
     * Overload those props as you need
     */
    protected $type     = 'sql';   // sql or raw
    protected $table    = '';      // if data source is sql, we need to specify the table name
    protected $table_pk = 'id';    // table primary key

    /**
     * Don't overload those props
     */
    protected $db; //link to $wpdb

    /**
     * Called after class construct
     */
    public function init() {}

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        $this->init();
    }

    /**
     * Return props
     * 
     * @tofix need validation
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Get a record
     * 
     * @param  mixed  $what   data to look for
     * @param  mixed  $column column name to look in
     * @param  string $field  field(s) to get, by default '*'
     * @return mixed
     */
    public function get($what, $column = null, $field = '*', $return_type = ARRAY_A)
    {
        $what = esc_sql($what);
        if(!isset($column)) $column = $this->table_pk;

        $query = 'SELECT '.$field.' FROM `'.$this->table.'` WHERE `'.$column.'` = "'.$what.'"';
        //echo $query;
        $result = $this->db->get_row($query, $return_type);

        return $result;
    }

}