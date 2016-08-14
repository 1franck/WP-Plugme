<?php

include_once dirname(__FILE__).'/plugme/plugme.list.table.php';

/**
 * Example of html list table
 */
class example_list_table extends plugme_list_table
{

    /**
     * list table settings
     */
    protected $default_orderby_column = 'name';
    protected $search_column          = 'id';
    protected $action_column          = 'id';

    protected $options = array(
        'singular'       => 'Person',
        'plural'         => 'Persons',
        'items_per_page' => 10,
    );

    protected $columns_header = array(
        'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
        'name'           => 'Name',
        'dateofbirth'    => 'Date of birth',
    );

    protected $sortable_columns = array(
        'title' => array('name',false),     //true means it's already sorted
    );

    protected $bulk_actions = array(
        'delete'    => 'Delete',
        // 'publish'   => 'Publier',
        // 'unpublish' => 'Dépublier',
    );

    /**
     * Set the sql query to be used
     */
    protected function set_data_query()
    {
        return 'SELECT * FROM `'.$this->data_source->table.'`';
    }

    /**
     * Custom bulk action
     */
    public function bulk_action_delete()
    {
        //do something
        wp_die('Items deleted (or they would be if we had items to delete)!');
    }

    public function column_profile_image($item)
    {
        return '<img width="75px" src="'.site_url().'/'.$item['profile_image'].'" />';
    }

}