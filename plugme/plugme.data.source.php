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
    private $columns = array();

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

    /**
     * Get total table items count
     * 
     * @return integer
     */
    public function count()
    {
        $c = 0;

        $count_result = $this->db->get_row('SELECT count('.$this->table_pk.') as c FROM `'.$this->table.'`', ARRAY_A);

        if(!empty($count_result)) $c = $count_result['c'];

        return $c;
    }

    /**
     * List columns from a table
     * 
     * @param  string $table 
     * @return array     
     */
    public function list_columns()
    {
        if(!empty($this->columns)) {
            foreach ( $this->db->get_col( "DESC " . $this->table, 0 ) as $cn ) {
                $this->columns[] = $cn;
            }
        }
        return $this->columns;
    }

    /**
     * Strip unwanted array key based on table column
     * 
     * @param  string $table 
     * @param  array $data  
     * @return array        
     */
    public function strip_unwanted_column($data)
    {
        $cols = $this->list_columns();
        $final = array();
        foreach($data as $k => $v){
            if(in_array($k, $cols)) $final[$k] = $v;
        }
        return $final;
    }

}