# Plugme

Copyright (c) 2016 François Lajoie Licensed under the MIT license (http://opensource.org/licenses/mit-license.php)

Plugme help you to quickly build Wordpress database driven plugin.

Plugme is not a Wordpress plugin, it's a plugin helper for developer.

In other words, Plugme let you build easily a table list and a form for navigating and managing your plugin data with the native visual style of Wordpress admin.


Highlights:
- easy to use
- native look of wordpress
- 11 form components including wordpress text editor (wp_editor), and wordpress media upload (wp_media)
- list table support pagination, column sorting, search, and bulk actions
- powerfull form validation
- extends form control and validation by adding your own


List of form controls type:
- Input (html input for text, password, email, url, ...)
- Textarea
- Texteditor (wp_editor)
- Checkbox
- Radio
- Select
- Media uploading/selecting (wp_media)
- Chosen (html select with jquery chosen)
- Datepicker (jquery-ui)
- Slider (jquery-ui)


## List table example:
![Plugme form example](https://github.com/1Franck/plugme/blob/master/screenshot-list-table.png?raw=true)

## Form example:
![Plugme form example](https://github.com/1Franck/plugme/blob/master/screenshot-form.png?raw=true)

## How it works

First, you need to define a data source by extending class `plugme_data_source`.
In your data source class, you need to define your table name and your primary key name.

```php
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

To generate a html table list to naviguate throw your table data, extends class `plugme_list_table`

```php
/**
 * Example of html list table
 */
class example_list_table extends plugme_list_table
{

    /**
     * list table settings
     */
    protected $default_orderby_column = 'name';
    protected $search_column          = 'name';
    protected $action_column          = 'name';

    protected $options = array(
        'singular'       => 'Person',
        'plural'         => 'Persons',
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
        'delete'    => 'Delete', // @see bulk_action_delete()
    );

    /**
     * Set the sql query base to be used
     */
    protected function set_data_query()
    {
        return 'SELECT * FROM `'.$this->data_source->table.'`';
    }

    /**
     * Custom bulk action 
     * Action name: delete
     */
    public function bulk_action_delete()
    {
        //do something
        wp_die('Items deleted (or they would be if we had items to delete)!');
    }

    /**
     * Custom rendering for column email
     
     * @param  array $item  table row data
     * @return string
     */
    public function column_email($item)
    {
        return '<a href="mailto:'.$item['email'].'">'.$item['email'].'</a>';
    }

}
```

To create a form with validation, extends class `plugme_form`

```php
/**
 * Form generation and validation example
 */
class example_form extends plugme_form
{
    protected $form_fields = array(

        'name' => array(
            'type'        => 'input',
            'label'       => 'Full name',
            'description' => 'Example of description for "text" input element.',
            'attrs'       => array(
                'required'    => false,
                'placeholder' => 'Full name',
            ),
            'validation' => array(
                'if_not_empty', //conditonnal validator, if empty, other validators are skipped
                'alpha' => array(
                    'options' => array('space' => true, 'punc' => array('-', '.')),
                    'msg'     => 'Enter a valid name'
                ),
        ),

        'email' => array(
            'type'        => 'input',
            'label'       => 'Email',
            'description' => 'Example of description for "email" input element.',
            'attrs'       => array(
                'required'    => true,
                'placeholder' => 'example@email.com',
                'type'        => 'email'
            ),
            validation => array(
                'email' => array(
                    'msg' => Enter a valid email address'
                )
            )
        ),

        // ...

    );

    /**
     * Called right after constructor
     */
    public function init() {}

    /**
     * Manipulate form data before saving it to database
     *
     * @param  array $data
     * @return array      
     */
    public function pre_save($data)
    {
        return $data;
    }
}
```

And finally, here a example of what might look your admin template. To make it more flexible, plugme doesn't do everything out of the box. This way, you can customize your plugin template page and integrate plugme anywhere more easily.

```php
<?php 

include_once dirname(__FILE__).'/plugme/plugme.admin.php'; 
include_once dirname(__FILE__).'/example.data.source.php'; 
include_once dirname(__FILE__).'/example.list.table.php';
include_once dirname(__FILE__).'/example.form.php'; 

$data_source = new example_data_source();

$list_table  = new example_list_table($data_source);
$form        = new example_form($data_source);


$plugme = plugme_admin::getInstance();
$plugme->register_list_table($list_table);
$plugme->register_form($form);


/**
 * Edit an item
 */
if($plugme->is_editing_item()) {
    $form->load_data_id($plugme->get_editing_id());
}
/**
 * Creating new item
 */
elseif($plugme->is_creating_item()) {
    $form->create_new_item();
}

/**
 * Form submitted
 */
if($plugme->is_form_submitted()) {

    if($form->validate()) {
        $form_pass = true;
    }
    else $form_failed = true;
}
?>

<div class="wrap">

<?php if($form->has_data() || $form->is_creating_item()) : ?>

    <!-- --------------- FORM ----------------- -->
    <h1>
        <?php
            $item = $list_table->get_option('singular');
            if($plugme->is_creating_item()) {
                echo __('New').' '.$item;
            }
            else {
                echo __('Edit').' '.$item .' '.$form->get_data($data_source->table_pk);
            }
        ?>
    </h1>

    <?php if(isset($form_failed)) : ?>
        <div class="notice notice-error is-dismissible" id="message">
            <p><?php _e('Form contain error(s)'); ?></p>
            <button class="notice-dismiss" type="button">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>

    <?php elseif(isset($form_pass)): ?>

        <div class="notice notice-success is-dismissible" id="message">
            <p><?php _e('Item saved'); ?></p>
            <button class="notice-dismiss" type="button">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>

    <?php endif; ?>

    <form id="person-form" class="form-wrap" method="post" enctype="multipart/form-data">

        <?php $form->generate_form('person-form'); ?>

    </form>

<?php else : ?>

    <!-- ------------ LIST TABLE -------------- -->
    <h1>
        <?php echo $list_table->get_option('plural'); ?>
        <a class="page-title-action" href="<?php echo $plugme->get_new_item_link(); ?>">
            <?php _e('Add new'); ?>
        </a>
    </h1>

    <form id="" method="post">
        <?php
            $list_table->prepare_items();
            $list_table->display();
        ?>
    </form>

<?php endif; ?>


</div>
```

