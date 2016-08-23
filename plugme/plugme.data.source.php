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
    protected $type             = 'sql'; // sql or raw
    protected $table            = '';    // if data source is sql, we need to specify the table name
    protected $table_pk         = 'id';  // table primary key
    protected $add_wp_db_prefix = true;  // will add wp_prefix on your table name on class contruct
    protected $sanitize = array(
        'filter' => FILTER_SANITIZE_STRING,
        'flags'  => array(FILTER_FLAG_STRIP_HIGH, FILTER_FLAG_STRIP_LOW)
    );

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
     * Called before save, after sanitizing
     */
    public function pre_save($data) {}

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        if($this->add_wp_db_prefix) {
            $this->table = $this->db->prefix.$this->table; 
        }

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
     * Save data
     * 
     * @param  array  $data
     * @param  boolean $sanitize
     * @return array 
     */
    public function save($data, $sanitize = true)
    {
        $data = $this->strip_unwanted_column($data);

        if($sanitize) {
            $data = $this->sanitize_data($data);
        }

        $data = $this->pre_save($data);

        //insert
        if(!array_key_exists($this->table_pk, $data) || 
            empty($data[$this->table_pk])) { 

            $this->db->insert(
                $this->table,
                $data
            );

            $data[$this->table_pk] = $this->db->insert_id;
        }
        else { //update

            $this->db->update(
                $this->table,
                $data,
                array(
                    $this->table_pk => $data[$this->table_pk]
                )
            );
        }

        return $data;
    }

    /**
     * Delete item(s) based on $table_pk column name
     * 
     * @param  string|array|int  $id     
     * @param  boolean $secure
     */
    public function delete($id, $secure = true)
    {
        if($secure && (!isset($id) || empty($id))) {
            return;
        }

        if(!is_array($id)) {
            $id = array($id);
        }

        foreach($id as $v) {
            $this->db->query(
                $this->db->prepare("DELETE FROM $this->table WHERE `$this->table_pk` = %s", $v)
            );
        }
    }

    /**
     * List columns from a table
     * 
     * @param  string $table 
     * @return array     
     */
    public function list_columns()
    {
        if(empty($this->columns)) {
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

    /**
     * Sanitize all data
     */
    public function sanitize_data($data)
    {
        return $this->sanitize_data_recursive($data, $this->sanitize['filter'], $this->sanitize['flags']);
    }

    /**
     * Make globalSanitize() recursive
     *
     * @param  array   $array
     * @param  integer $filter
     * @param  integer $flags
     * @return array
     */
    private function sanitize_data_recursive($array, $filter, $flags)
    {
        foreach($array as $k => $v) {
            if(!is_array($v)) $array[$k] = filter_var($v, $filter, $flags);
            else {
                $array[$k] = $this->sanitize_data_recursive($v, $filter, $flags);
            }
        }
        return $array;
    }

}