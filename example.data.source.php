<?php

include_once dirname(__FILE__).'/plugme/plugme.data.source.php';


/**
 * Example of data source class
 */
class example_data_source extends plugme_data_source
{
    protected $table = 'plugme_example';
    protected $table_primary_column = 'id';

    // called after class construct
    public function init() {}

    // called before save() after sanitizing
    public function pre_save($data)
    {
        if(!array_key_exists('enabled', $data)) $data['enabled'] = 0;

        return $data;
    }
}