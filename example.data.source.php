<?php

include_once dirname(__FILE__).'/plugme/plugme.data.source.php';


/**
 * Example of data source class
 */
class example_data_source extends plugme_data_source
{
    protected $table_primary_column = 'id';

    public function init()
    {
        $this->table = $this->db->prefix.'plugme_example';
    }
}