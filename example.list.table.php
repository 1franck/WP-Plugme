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
    protected $search_column          = array('name', 'occupation');
    protected $action_column          = 'name';

    protected $options = array(
        'singular'       => 'Person',
        'plural'         => 'Persons',
        'items_per_page' => 6,
    );

    protected $columns_header = array(
        'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
        'name'           => 'Name',
        'occupation'     => 'Occupation',
        'gender'         => 'Gender',
        'dateofbirth'    => 'Date of birth',
        'email'          => 'Email',
        'image'          => 'Profile image',
    );

    protected $sortable_columns = array(
        'name', 
        'gender',
        'dateofbirth'
    );

    protected $bulk_actions = array(
        'delete'    => 'Delete',
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
        $data = $this->get_bulk_action_data();
        if(!empty($data)) {
            $this->data_source->delete($data);
            echo '
                <div class="notice notice-success is-dismissible" id="message">
                    <p>Items '.(is_array($data) ? implode(', ',$data) : $data).' deleted</p>
                    <button class="notice-dismiss" type="button">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>';
        }
    }

    public function column_image($item)
    {
        if(empty($item['image'])) return 'No image yet';
        return '<img width="75px" src="'.$item['image'].'" />';
    }

    public function column_email($item)
    {
        return '<a href="mailto:'.$item['email'].'">'.$item['email'].'</a>';
    }

}