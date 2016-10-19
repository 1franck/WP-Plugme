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
                    echo __('Add').' '.$item;
                }
                else {
                    echo __('Edit').' '.$item .' '.$form->get_data($data_source->table_pk);
                }
            ?>
        </h1>

        <form id="data-form" class="form-wrap" method="post" enctype="multipart/form-data">

            <?php $form->generate_form('data-form'); ?>

        </form>

    <?php else : ?>

        <!-- LIST TABLE -->
        <h1>
            <?php echo $list_table->get_option('plural'); ?>
            <a class="page-title-action" href="<?php echo $plugme->get_new_item_link(); ?>">
                <?php _e('Add'); ?>
            </a>
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

        <form id="data-list-table" method="post" action="<?php echo admin_url('admin.php?page='.$_GET['page']); ?>">
            <?php
                $list_table->prepare_items();
                $list_table->display();
            ?>
        </form>

    <?php endif; ?>


</div>