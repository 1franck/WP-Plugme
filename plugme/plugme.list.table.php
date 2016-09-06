<?php

/**
 * Plugme List Table
 *
 * @author  Francois Lajoie
 */

include_once dirname(__FILE__).'/plugme.wp.list.table.php';


abstract class plugme_list_table extends plugme_wp_list_table
{

    /**
     * Overload those props as you need
     */
    protected $default_orderby_column = '';    // default column used for ordering. if empty, $data_source->table_pk will be used
    protected $search_column          = '';    // column name used for search. if empty, $data_source->table_pk will be used
    protected $action_column          = '';    // define wich column will receive action(s) link(s) @see default_action_column()

    protected $columns_header         = array(); // table columns header caption
    protected $sortable_columns       = array(); // specify which column can be sorted
    protected $bulk_actions           = array(); // bulk action dropdown item
    protected $column_actions         = array(
        'edit'   => true,
        'delete' => true,
    );

    protected $options = array(); // options @see $default_options

    /**
     * Don't overload those props
     */
    
    protected $db;                // link to wordpress $wpdb
    protected $data_source;       // link to a plugme_data_source instance
    protected $data_source_query; // database query generated
    protected $default_options = array(   // defaults options
        'singular'         => '',
        'plural'           => '',
        'items_per_page'   => 10,
        'show_search_box'  => true,
        'show_bulk_action' => true,
    );



    /**
     * Set the list data. Should return an array
     *
     * used when $data_source is set to 'raw'
     */
    protected function set_data() {}

    /**
     * Set the list data. Should return an array
     *
     * used when $data_source is set to 'sql'
     */
    protected function set_data_query() {}

    /**
     * Called after class construct
     */
    public function init() {}

    /**
     * Constructor
     */
    public function __construct(plugme_data_source $data_source)
    {
        global $status, $page, $wpdb;

        $this->db = $wpdb;

        $this->data_source = $data_source;

        if(empty($this->search_column)) {
            $this->search_column = $data_source->table_pk;
        }
        if(empty($this->default_orderby_column)) {
            $this->default_orderby_column = $data_source->table_pk;
        }

        $this->options = array_merge($this->default_options, $this->options);

        //Set parent defaults
        parent::__construct($this->options);

        $this->init();
    }

    /**
     * Default column data
     * 
     * @param  array  $item
     * @param  string $cn      
     */
    public function column_default($item, $cn)
    {
        switch($cn) {
            case $this->action_column : 
                return $this->default_action_column($item);
                break;
            default:
                return $item[$cn];
        }
    }

    /**
     * Add action(s) link(s) to a default cell ($action_column)
     *
     * @return  string
     */
    function default_action_column($item)
    {        
        //Build row actions
        $actions = array(
            'edit'      => sprintf(
                '<a href="?page=%s&action=%s&'.strtolower($this->options['singular']).'=%s">'.__('Edit').'</a>',
                $_REQUEST['page'],
                'edit',
                $item[$this->data_source->table_pk]
            ),
            'delete'    => sprintf(
                '<a href="?page=%s&action=%s&'.strtolower($this->options['singular']).'=%s">'.__('Delete').'</a>',
                $_REQUEST['page'],
                'delete',
                $item[$this->data_source->table_pk]
            ),
        );

        if(!$this->column_actions['edit']) unset($actions['edit']);
        if(!$this->column_actions['delete']) unset($actions['delete']);

        //column has already custom column method
        $custom_col_method = 'column_'.$this->action_column;
        $data = $item[$this->action_column];
        if(method_exists($this, $custom_col_method)) {
            $data = $this->$custom_col_method($item);
        }

        //Return the column cell contents
        return sprintf('<b>%1$s</b> <span style="color:silver">(#%2$s)</span>%3$s',
            /*$1%s*/ $data,
            /*$2%s*/ __($item[$this->data_source->table_pk]),
            /*$3%s*/ $this->row_actions($actions)
        );
    }


    /**
     * Checkbox column
     * 
     * @param  mixed $item
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item[$this->data_source->table_pk] //The value of the checkbox should be the record's id
        );
    }

    /**
     * REQUIRED! This method dictates the table's columns and titles. 
     * 
     * @return array
     */
    public function get_columns()
    {
        return $this->columns_header;
    }

    /**
     * Return all options
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
     * Get bulk action data
     * 
     * @return array
     */
    public function get_bulk_action_data()
    {
        $keyname = strtolower($this->options['singular']);
        $source = array();

        if(isset($_POST) && !empty($_POST)) {
            $source = $_POST;
        }
        elseif(isset($_GET) && !empty($_GET)) {
            $source = $_GET;
        }

        if(array_key_exists($keyname, $source)) {
            return $source[$keyname];
        }

        return array();
    }

    /**
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here.
     * 
     * @return array
     */
    public function get_sortable_columns() 
    {
        $final = array();

        // new example:
        // $sortable_columns = array(
        //     'name',     
        //     'address',
        // );

        if(!empty($this->sortable_columns)) {
            foreach($this->sortable_columns as $k => $v) {
                $def_sort = ($v === $this->default_orderby_column) ? true : false;
                $final[$v] = array($v, $def_sort);
            }
        }

        // $final =  $this->sortable_columns
        //
        // old example:
        // (first param = url $_GET name)
        // (second param = boolean: true means it's already sorted)
        // 
        // $sortable_columns = array(
        //     'name'     => array('name',true),     
        //     'address'  => array('adress',false),
        // );
        return $final;
    }

    /**
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them.
     * 
     * @return array
     */
    public function get_bulk_actions() 
    {
        // translate
        if(!empty($this->bulk_actions)) {
            foreach($this->bulk_actions as $k => $v) {
                $this->bulk_actions[$k] = __($v);
            }
        }

        return $this->bulk_actions;
    }

    /**
     * Handle your bulk actions. 
     * It look for bulk_action_[youractionname] in order execute a bulk action
     * 
     * @see $this->prepare_items()
     */
    protected function process_bulk_action() 
    {        
        $cur = $this->current_action();
        
        if(!empty($this->bulk_actions) && !empty($cur)) {
            if(array_key_exists($cur, $this->bulk_actions)) {
                $method = 'bulk_action_'.$cur;
                if(method_exists($this, $method)) {
                    $this->$method();
                }
            }
        }        
    }

    /**
     * REQUIRED! This is where you prepare your data for display. 
     */ 
    public function prepare_items() 
    {
        $total_items = 0;

        $per_page = $this->options['items_per_page'];
        
        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $current_page = $this->get_pagenum();

        // data from database
        if($this->data_source->type === 'sql') {
            $data = $this->_sql_data_source($current_page);

            // $count_result = $this->db->get_row( 
            //     'SELECT count('.$this->data_source->table_pk.') as c FROM `'.$this->data_source_table.'`', 
            //     ARRAY_A
            // );
            // if(!empty($count_result)) {
            //     $total_items = $count_result['c'];
            // }

            $total_items = $this->data_source->count();

        }
        //date from array and cie
        elseif($this->data_source->type === 'raw') {
            $data = $this->set_data();
            $total_items = count($data);
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        }
        else {
            wp_die(__CLASS__.': Invalid data source provider! Must be \'sql\' or \'raw\' only');
        }

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    /**
     * Create the appropriate sql query
     * 
     * @param  integer $current_page
     * @return array
     */
    private function _sql_data_source($current_page = 1)
    {
        $query = $this->set_data_query();

        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : $this->default_orderby_column; //If no sort, default to title
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc

        if(!empty($_POST['lookfor'])) {
            $lookfor = $this->db->esc_like($_POST['lookfor']);
            $query .= ' WHERE `'.$this->search_column.'` LIKE "%'.$lookfor.'%"';
        }

        $query .= ' ORDER BY '.esc_sql($orderby).' '.esc_sql($order);

        $offset = ($current_page - 1) * $this->options['items_per_page'];
        $count = $this->options['items_per_page'];

        $query .= ' LIMIT '.$offset.','.$count;

        $this->data_source_query = $query;

        //echo $query;

        return $this->db->get_results($query, ARRAY_A);
    }

    /**
     * Gather and sort data from a raw source(ie array data)
     * 
     * @return array
     */
    private function _raw_data_source()
    {
        $data = $this->set_data();

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        $_REQUEST['plugme_default_orderby_column'] = $this->default_orderby_column;
        function usort_reorder($a,$b){
            global $plugme_default_orderby_column;
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : $_REQUEST['plugme_default_orderby_column']; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');

        return $data;
    }

    /**
     * Display the table
     */
    public function display()
    {
        echo '
            <input type="hidden" name="page" value="'.$_REQUEST['page'].'" />
            '.parent::display();
    }
}