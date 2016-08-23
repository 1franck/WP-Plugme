<?php
/**
 * Plugme form
 *
 * @author  Francois Lajoie
 */

include 'plugme.form.control.php';
include 'plugme.form.validation.php';

abstract class plugme_form
{
    /**
     * Overload those props as you need
     */
    protected $form_fields = array();

    /**
     * Examples for $form_fields
     */
    /*
    
    'title' => array(
        'type'     => 'text', 
        'label'    => __('Title'),
        'required' => true,
    ),

    'enabled' => array(
        'type'     => 'select', 
        'label'    => __('Title'),
        'required' => true,
        'options'  => array(
            '0' => __('No'),
            '1' => __('Yes'),
        )
    ),



     */


    /**
     * Don't overload those props
     */
    protected $db;                   // link to $wpdb
    protected $data_source;          // plugme_data_source instance
    protected $data       = array(); // loaded data
    protected $new_item   = false;   // is a new item
    protected $assets_url;           // plugme assets url
    protected $has_errors = false;   // form has error @see

    /**
     * Called after class construct
     */
    public function init() {}

    /**
     * Constructor
     */
    public function __construct(plugme_data_source $data_source)
    {
        global $wpdb;

        $this->db = $wpdb;

        $this->data_source = $data_source;

        $this->validation = new plugme_form_validation();

        $this->init();

        $this->assets_url = plugins_url(basename(realpath(dirname(__FILE__).'/../')).'/plugme/assets');

        //echo $this->assets_url;
        
        $this->register_assets();

        $this->wp_magic_quotes();
    }

    /**
     * Register plugme assets for forms
     */
    private function register_assets()
    {
        //jquery ui style
        wp_register_style("plugme-jquery-ui", $this->assets_url.'/jquery-ui.css');
        wp_enqueue_style( 'plugme-jquery-ui', time(), true);

        //parsley (http://parsleyjs.org)
        wp_register_script("plugme-parsley", $this->assets_url.'/parsley.min.js');
        wp_enqueue_script('plugme-parsley', time(), true);
    }

    /**
     * Nasty wordpress go!
     */
    private function wp_magic_quotes() 
    {
        $_POST = array_map( 'stripslashes_deep', $_POST);
    }

    /**
     * Load an item from the database base on $primary_column
     * 
     * @param mixed $id
     */
    public function load_data_id($id)
    {
        $this->data = $this->data_source->get($id);

        if(empty($this->data)) {
            wp_die(__CLASS__.': No record found for '.$this->data_source->table_pk.'['.esc_sql($id).']');
        }
    }


    /**
     * Set the form data
     * 
     * @param array  $data     
     * @param boolean $sanitize 
     */
    public function set_data($data, $sanitize = true) 
    {
        if($sanitize) {
            $data = $this->data_source->sanitize_data($data);
        }
        $this->data = $data;
    }

    /**
     * Check if data empty
     * 
     * @return boolean
     */
    // public function has_data()
    // {
    //     if(!empty($this->data)) return true;
    //     return false;
    // }

    /**
     * Get field data
     * 
     * @param  string $name
     * @return mixed      
     */
    public function get_data($name)
    {
        if($this->new_item && !array_key_exists($name, $this->data)) {
            if(array_key_exists($name, $this->form_fields)) {
                if(array_key_exists('default', $this->form_fields[$name])) {
                    return $this->form_fields[$name]['default'];
                }
            }
            return '';
        }

        if(!array_key_exists($name, $this->data)) {
            if($name !== 'submit') {
                wp_die(__CLASS__.': Field "'.$name.'" doesn\'t exists');
            }
        }
        else {
            return stripslashes($this->data[$name]);
        }
        
    }

    /**
     * Check if its a new item
     * 
     * @return boolean
     */
    public function is_creating_item()
    {
        return $this->new_item;
    }

    /**
     * New item
     */
    public function create_new_item()
    {
        $this->new_item = true;
    }


    /**
     * Generate the form
     * 
     * @param string $form_id_name
     */
    public function generate_form($form_id_name = null)
    {
        if(!empty($this->form_fields)) {

            $errors = $this->validation->get_errors();
            $first_field = '';
            $save_btn = ($this->new_item) ? __('Save') : __('Save changes');

            if(!empty($errors)) {
                echo '
                    <div class="notice notice-error is-dismissible" id="message">
                        <p>'.__('Form contains error(s)').'</p>
                        <button class="notice-dismiss" type="button">
                            <span class="screen-reader-text">Dismiss this notice.</span>
                        </button>
                    </div>';
            }

            // form buttons
            echo '
                <div class="alignright">
                    <a href="?page='.$_REQUEST['page'].'" class="button">'.__('Cancel').'</a> &nbsp;
                    <input type="submit" value="'.$save_btn.'" class="button button-primary" id="submit" name="submit">
                </div><br><br>
                <hr class="clear">';

            echo '<table class="form-table"><tbody>';

            foreach($this->form_fields as $k => $v) {

                if(empty($first_field)) $first_field = $k;

                if(!array_key_exists('type', $v)) $v['type'] = 'input';

                $control_cn = 'plugme_form_control_'.$v['type'];
                if(!class_exists($control_cn, false)) {
                    $form_control_file = dirname(__FILE__).'/form/control/'.$v['type'].'.php';
                    if(!file_exists($form_control_file)) {
                        if(!WP_DEBUG) continue;
                        else wp_die(__CLASS__.': control type "'.$v['type'].'" not found');
                    }
                    else {
                        include $form_control_file;
                        if(!class_exists($control_cn, false)) {
                            if(!WP_DEBUG) continue;
                            else wp_die(__CLASS__.': control type class "'.$control_cn.'" not found in '.basename($form_control_file));
                        }
                    }                   
                }

                $control = new $control_cn($k, $this->get_data($k), $v);

                $component = $control->generate();
                $label     = $control->get_option('label');
                $desc      = $control->get_option('description');

                $error_class = '';
                $error_msg = '';
                if(array_key_exists($k, $errors)) {
                    $error_class = 'error';
                    $error_msg = '<p class="error">'.$errors[$k].'</p>';
                }

                echo '<tr>
                        <th scope="row">
                            <label class="'.$error_class.'" for="field-'.$k.'">'.__($label).'</label>
                        </th>
                        <td>'.$component.'<p class="description">'.__($desc).'</p>'.$error_msg.'</td>
                    </tr>'; 
            }

            echo '</tbody></table>';

            // existing item
            if(!$this->new_item) {
                echo '<input type="hidden" name="'.$this->data_source->table_pk.'" value="'.$this->get_data($this->data_source->table_pk).'">';
            }

            // form buttons
            
            echo '
                <hr>
                <div class="alignleft">
                    <a href="?page='.$_REQUEST['page'].'" class="button">'.__('Cancel').'</a> &nbsp;
                    <input type="submit" value="'.$save_btn.'" class="button button-primary" id="submit" name="submit">
                </div>';


            // script (auto focus & parsley validation)
            echo '
                <script>
                jQuery(function() {
                    jQuery("#field-'.$first_field.'").focus();
                    jQuery("#'.$form_id_name.'").parsley();
                });
                </script>
                <style><!--
                .error {
                    color:#cc0000 !important;
                }
                -->
                </style>
            ';
           
        }
    }

    /**
     * Simple validation
     *
     * @param  array $data
     * @return boolean
     */
    public function validate($data)
    {
        $r = true;

        $this->data = $this->data_source->strip_unwanted_column($data);

        return $this->validation->validate($this->form_fields, $data);
    }

    /**
     * Get form validation errors
     * 
     * @return array
     */
    public function get_errors()
    {
        return $this->validation->get_errors();
    }

    /**
     * Check if has errors
     * 
     * @return boolean
     */
    public function has_errors()
    {
        $errors = $this->has_errors;
        return empty($errors);
    }

    /**
     * Save data @see plugme_data_source::save() 
     * 
     * @param  array   $data    
     * @param  boolean $sanitize
     * @return array           
     */
    public function save_data($data, $sanitize = true)
    {
        $data = $this->data_source->save($data, $sanitize = true);
        $this->flush_data();

        return $data;
    }

    /**
     * Erase current data
     */
    public function flush_data()
    {
        $this->data = array();
    }

    /**
     * Return country list array for select control
     * @see  data/countries.php
     * 
     * @return array
     */
    public function get_countries_list()
    {
        return include 'data/countries.php';
    }

}