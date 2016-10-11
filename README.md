# Plugme

**Plugme help you to quickly build Wordpress database driven plugin.**

**Plugme is not a Wordpress plugin, it's a plugin helper for developer.**

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
- Datetimepicker (jquery datetimepicker)
- Slider (jquery-ui)


## List table example:
![Plugme form example](https://github.com/1Franck/plugme/blob/master/screenshot-list-table.png?raw=true)

## Form example:
![Plugme form example](https://github.com/1Franck/plugme/blob/master/screenshot-form.png?raw=true)

## How it works

Copy `/plugme` folder inside your plugin folder (ex: wp-content/plugins/mysuperplugin/plugme)

Now that you are ready to use Plugme, you need to define a data source by extending class `plugme_data_source`. In your data source class, set your table name and your primary key name.

@see example.data.source.php
```php
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
```

To generate a html table list to naviguate through your table data, extends class `plugme_list_table`

@see example.list.table.php
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
    protected $search_column          = array('name', 'occupation');
    protected $action_column          = 'name';

    protected $options = array(
        'singular'       => 'Person',
        'plural'         => 'Persons',
        'items_per_page' => 10,
    );

    protected $columns_header = array(
        'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
        'name'           => 'Name',
        'occupation'     => 'Occupation',
        'gender'         => 'Gender',
        'dateofbirth'    => 'Date of birth',
        'email'          => 'Email',
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

@see example.form.php
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
            'validation' => array(
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

@see adminpage.tpl.php
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
 * Form submitted
 */
if($plugme->is_form_submitted($_POST)) {

    if($form->validate($_POST)) {
        $form_pass = true;
        $item_saved = $form->save_data($_POST);
        $form->flush_data();
        // if after saving an item, you want to come back to the list table, 
        // you need to delete param action. If you want to add an item right after
        // another one, comment this line
        unset($_GET['action']);
    }
    else {
        // repush data to the form so we can continue editing it
        $form->set_data($_POST); 
    }
}


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


?>

<div class="wrap">

    <?php if($plugme->is_editing_item() || $plugme->is_creating_item()) : ?>

        <!-- FORM -->
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


        <?php if(isset($form_pass)): ?>
            <!-- notice -->
            <div class="notice notice-success is-dismissible" id="message">
                <p><?php _e('Item saved').' (#'.$item_saved['id'].' - '.$item_saved['name'].')'; ?></p>
                <button class="notice-dismiss" type="button">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>

        <?php endif; ?>

        <form id="person-form" class="form-wrap" method="post" enctype="multipart/form-data">

            <?php $form->generate_form('person-form'); ?>

        </form>

    <?php else : ?>

        <!-- LIST TABLE -->
        <h1>
            <?php echo $list_table->get_option('plural'); ?>
            <a class="page-title-action" href="<?php echo $plugme->get_new_item_link(); ?>">
                <?php _e('Add new'); ?>
            </a>
        </h1>

        <form id="person-list-table" method="post">
            <?php
                $list_table->prepare_items();
                $list_table->display();
            ?>
        </form>

    <?php endif; ?>


</div>
```

Copyright (c) 2016 François Lajoie Licensed under the MIT license (http://opensource.org/licenses/mit-license.php)