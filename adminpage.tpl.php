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




// include_once dirname(__FILE__).'/../plugme/plugme.php'; 
// include_once dirname(__FILE__).'/../movies.list.data.php'; 
// include_once dirname(__FILE__).'/../movies.form.data.php'; 

// $plugme = plugme::getInstance();

// $list_table = new themoviesdb_movies_list_data();
// $form = new themoviesdb_movies_form_data();

// $plugme->register_list_table($list_table);
// $plugme->register_form($form);

// /**
//  * Edit an item
//  */
// if($plugme->is_editing_item()) {
//     $form->load_data_id($plugme->get_editing_id());
// }
// /**
//  * Creating new item
//  */
// elseif($plugme->is_creating_item()) {
//     $form->create_new_item();
// }


// /**
//  * Check if form submitted
//  */
// if($plugme->is_form_submitted()) {
//     if($form->validate()) {
//         $form->save_data();
//         $form->flush_data();
//         $save_success = true;
//     }
//     else {
//         $form_error = true;
//         $form->set_data($_POST);
//     }
// }
// 

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

    <form id="person-form" method="post" enctype="multipart/form-data">

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