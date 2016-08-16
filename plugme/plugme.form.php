<?php
/**
 * PlugMe form generator
 *
 * @author  Francois Lajoie
 */

include 'plugme.form.control.php';

/**
 * Include form controls components
 */
foreach(new DirectoryIterator(dirname(__FILE__).'/form') as $f) {
    if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), array('php'))) {
        include 'form/'.$f;
    }
}

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
     * Set the list data. Should return an array
     */
    protected function set_data($data) 
    {
        $this->data = $data;
    }

    /**
     * Called after class construct
     */
    public function init() {}

    /**
     * Called before updating/inserting item
     * Must return $data
     */
    public function pre_save($data)
    {
        return $data;
    }

    /**
     * Constructor
     */
    public function __construct(plugme_data_source $data_source)
    {
        global $wpdb;

        $this->db = $wpdb;

        $this->data_source = $data_source;

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
        //jquery ui
        wp_register_script("plugme-jquery-ui", $this->assets_url.'/jquery-ui.min.js');
        wp_register_style("plugme-jquery-ui", $this->assets_url.'/jquery-ui.css');
        wp_enqueue_script('plugme-jquery-ui', time(), true);
        wp_enqueue_style( 'plugme-jquery-ui', time(), true);

        //chosen jquery (https://github.com/harvesthq/chosen)
        wp_register_script("plugme-chosen", $this->assets_url.'/chosen/chosen.jquery.min.js');
        wp_register_style("plugme-chosen", $this->assets_url.'/chosen/chosen.min.css');
        wp_enqueue_script('plugme-chosen', time(), true);
        wp_enqueue_style( 'plugme-chosen', time(), true);

        //on/off switch
        wp_register_style("plugme-onoffswitch", $this->assets_url.'/onoffswitch.css');
        wp_enqueue_style( 'plugme-onoffswitch', time(), true);

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
     * Check if data empty
     * 
     * @return boolean
     */
    public function has_data()
    {
        if(!empty($this->data)) return true;
        return false;
    }

    public function has_errors()
    {
        return $this->has_errors;
    }

    /**
     * Get field data
     * 
     * @param  string $name
     * @return mixed      
     */
    public function get_data($name)
    {
        if($this->new_item) {
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

            $first_field = '';
            $save_btn = ($this->new_item) ? __('Save') : __('Save changes');

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

                if(!array_key_exists('type', $v)) $v['type'] = 'text';

                $control_cn = 'plugme_form_control_'.$v['type'];
                if(!class_exists($control_cn, false)) {
                    if(!WP_DEBUG) continue;
                    else wp_die(__CLASS__.': control type "'.$v['type'].'" not found');
                }

                $control = new $control_cn($k, $this->get_data($k), $v);

                $component = $control->generate();
                $label     = $control->get_option('label');
                $desc      = $control->get_option('description');
 
                echo '<tr>
                        <th scope="row">
                            <label for="field-'.$k.'">'.__($label).'</label>
                        </th>
                        <td>'.$component.'<p class="description">'.__($desc).'</p></td>
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
            ';
           
        }
    }

    /**
     * Simple validation
     * 
     * @return boolean
     */
    public function validate()
    {
        $r = true;

        if(empty($_POST)) $r = false;
        else {
            foreach($_POST as $k => $v) {
                $field = $this->get_data($k);
                if(array_key_exists($k, $this->form_fields)) {
                    if(array_key_exists('required', $this->form_fields[$k]) && $this->form_fields[$k]['required']) {
                        if(empty($v)) $r = false;
                    } 
                }
            }
        }

        $this->has_errors = !$r;

        return $r;
    }


    public function save_data()
    {
        if(empty($_POST[$this->data_source->table_pk])) {
            //insert

            $data_to_save = $this->data_source->strip_unwanted_column($_POST);

            $data_to_save = $this->pre_save($data_to_save);

            $this->db->insert(
                $this->data_source_table,
                $data_to_save
            );
        }
        else {
            //update
            
            $data_to_save = $this->data_source->strip_unwanted_column($_POST);

            $data_to_save = $this->pre_save($data_to_save);

            $this->db->update(
                $this->data_source_table,
                $data_to_save,
                array(
                    $this->data_source->table_pk => $_POST[$this->data_source->table_pk]
                )
            );
        }
    }

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