<?php
/**
 * PlugMe form generator
 *
 * @author  Francois Lajoie
 */
abstract class plugme_form
{
    /**
     * Overload those props as you need
     */
    protected $data_source_table = '';
    protected $primary_column    = 'id';    // primary key column name
    protected $form_fields       = array();

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
    protected $db; //link to $wpdb
    protected $data = array(); // loaded data
    protected $new_item = false;
    protected $assets_url;
    protected $has_errors = false;


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
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

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
    }

    /**
     * Nasty wordpress go!
     */
    private function wp_magic_quotes() 
    {
        $_POST = array_map( 'stripslashes_deep', $_POST);
    }

    /**
     * List columns from a table
     * 
     * @param  string $table 
     * @return array     
     */
    private function list_column()
    {
        $cols = array();
        foreach ( $this->db->get_col( "DESC " . $this->data_source_table, 0 ) as $cn ) {
            $cols[] = $cn;
        }
        return $cols;
    }

    /**
     * Strip unwanted array key based on table column
     * 
     * @param  string $table 
     * @param  array $data  
     * @return array        
     */
    private function strip_unwanted_column($data)
    {
        $cols = $this->list_column();
        $final = array();
        foreach($data as $k => $v){
            if(in_array($k, $cols)) $final[$k] = $v;
        }
        return $final;
    }

    /**
     * Check if string is json string
     * 
     * @param  string $string 
     * @return boolean         
     */
    private function is_json($string) 
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Load an item from the database base on $primary_column
     * 
     * @param mixed $id
     */
    public function load_data_id($id)
    {
        $id = esc_sql($id);

        $query = 'SELECT * FROM `'.$this->data_source_table.'` WHERE `'.$this->primary_column.'` = "'.$id.'"';
        //echo $query;
        $result = $this->db->get_row($query, ARRAY_A);

        if(empty($result)) {
            wp_die('No record found for '.$this->primary_column.'['.$id.']');
        }

        //print_r($result);

        $this->data = $result;
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
            if($name !== 'submit') wp_die('Field '.$name.' doesn\'t exists');
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
     */
    public function generate_form()
    {
        if(!empty($this->form_fields)) {

            // form buttons
            echo '
                <div class="alignright">
                    <a href="?page='.$_REQUEST['page'].'" class="button">'.__('Cancel').'</a> &nbsp;
                    <input type="submit" value="'.__('Save').'" class="button button-primary" id="submit" name="submit">
                </div><br><br>
                <hr class="clear">';

            echo '<table class="form-table"><tbody>';

            foreach($this->form_fields as $k => $v) {

                if(!array_key_exists('type', $v)) $v['type'] = 'text';
                $method = 'form_'.$v['type'];

                $component = $this->$method($k, $this->get_data($k), $v);

                $description = (array_key_exists('description', $v)) ? $v['description'] : '';

                echo '<tr>
                        <th scope="row">
                            <label for="field-'.$k.'">'.__($v['label']).'</label>
                        </th>
                        <td>'.$component.'<br><small>'.$description.'</small></td>
                    </tr>'; 
            }

            echo '</tbody></table>';

            // existing item
            if(!$this->new_item) {
                echo '<input type="hidden" name="'.$this->primary_column.'" value="'.$this->get_data($this->primary_column).'">';
            }

            // form buttons
            echo '
                <hr>
                <div class="alignright">
                    <a href="?page='.$_REQUEST['page'].'" class="button">'.__('Cancel').'</a> &nbsp;
                    <input type="submit" value="'.__('Save').'" class="button button-primary" id="submit" name="submit">
                </div>';
           
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
        if(empty($_POST[$this->primary_column])) {
            //insert

            $data_to_save = $this->strip_unwanted_column($_POST);

            $data_to_save = $this->pre_save($data_to_save);

            $this->db->insert(
                $this->data_source_table,
                $data_to_save
            );
        }
        else {
            //update
            
            $data_to_save = $this->strip_unwanted_column($_POST);

            $data_to_save = $this->pre_save($data_to_save);

            $this->db->update(
                $this->data_source_table,
                $data_to_save,
                array(
                    $this->primary_column => $_POST[$this->primary_column]
                )
            );
        }
    }

    public function flush_data()
    {
        $this->data = array();
    }

    /**
     * Text control (input)
     */
    public function form_text($name, $data, $options = null)
    {
        return '<input id="field-'.$name.'" class="regular-text" type="text" value="'.$data.'" name="'.$name.'">';
    }

    /**
     * Textarea control
     */
    public function form_textarea($name, $data, $options = null)
    {
        return '<textarea id="field-'.$name.'" name="'.$name.'" class="large-text" rows="3">'.$data.'</textarea>';
    }

    /**
     * Text editor (use wp_editor)
     */
    public function form_texteditor($name, $data, $options = null)
    {
        $settings = array(
            'teeny' => true,
            'textarea_rows' => 8,
            'tabindex' => 1,
            'media_buttons' => false
        );
        ob_start();
        wp_editor(stripslashes($data), $name, $settings);
        $wp_editor = ob_get_contents();
        ob_end_clean();

        return $wp_editor;
    }

    /**
     * Checkbox control
     */
    public function form_checkbox($name, $data, $options = null)
    {
        return '<input id="field-'.$name.'" type="checkbox" value="1" '.(($data === 1) ? 'checked' : '').' name="'.$name.'">';;
    }

    /**
     * Switch control (use checkbox and onoffswitch.css)
     */
    public function form_switch($name, $data, $options = null)
    {
        return '
            <div class="onoffswitch">
                <input type="checkbox" name="'.$name.'" class="onoffswitch-checkbox" id="field-'.$name.'" value="1" '.(($data == 1) ? 'checked' : '').'>
                <label class="onoffswitch-label" for="field-'.$name.'"></label>
            </div>';
    }

    /**
     * Select control
     */
    public function form_select($name, $data, $options = null)
    {
        $control = '<select id="field-'.$name.'" name="'.$name.'">';
        if(array_key_exists('options', $options) && !empty($options['options'])) {
            foreach($options['options'] as $k => $v) {
                $control .= '<option '.(($k == $data) ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
            }
        }
        
        $control .= '</select>';

        return $control;
    }

    /**
     * Image uploading/selecting (use wp_media)
     */
    public function form_image($name, $data, $options = null)
    {
        $id_name = 'field-'.$name;
        $image_url = site_url().'/'.$data;
        $control = '
            <input type="text" id="'.$id_name.'" name="'.$name.'" class="regular-text" value="'.$data.'">
            <input type="button" name="upload-btn" id="upload-btn-'.$id_name.'" class="button-secondary" value="'.__('Upload image').'">
            <br><img id="image-'.$id_name.'" src="'.$image_url .'" width="300px">';


        $control .= '
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    $("#upload-btn-'.$id_name.'").click(function(e) {
                        e.preventDefault();
                        var image = wp.media({ 
                            title: "'.__('Upload image').'",
                            // mutiple: true if you want to upload multiple files at once
                            multiple: false
                        }).open()
                        .on("select", function(e){
                            // This will return the selected image from the Media Uploader, the result is an object
                            var uploaded_image = image.state().get("selection").first();
                            var site_url = "'.site_url().'";
                            // We convert uploaded_image to a JSON object to make accessing it easier
                            // Output to the console uploaded_image
                            console.log(uploaded_image);
                            var image_url = uploaded_image.toJSON().url;
                            console.log(image_url);
                            var real_image_url = image_url.replace("images/", "'.site_url().'/wp-content/uploads/");
                            // Let"s assign the url value to the input field
                            $("#'.$id_name.'").val(image_url.replace("'.site_url().'", ""));
                            $("#image-'.$id_name.'").attr("src", real_image_url);

                        });
                    });
                });
            </script>';    

        wp_enqueue_media();
        return $control;
    }

    /**
     * Datepicker
     *
     * @uses  jquery-ui
     */
    public function form_datepicker($name, $data, $options = null)
    {
        $id_name = 'field-'.$name;
        $format = (array_key_exists('format', $options)) ? $options['format'] : 'yy-mm-dd';
        $control = '
            <input type="date" id="'.$id_name.'" name="'.$name.'" value="'.$data.'" />
            <span class="dashicons dashicons-calendar-alt" style="margin-left:-30px;margin-top:5px;pointer-events: none;"></span>
            <script>
                jQuery(document).ready(function(){
                    jQuery("#'.$id_name.'").datepicker({ dateFormat: "'.$format.'" }); 
                });
            </script>';

        return $control;
    }

    /**
     * Chosen
     *
     * @uses  jquery chosen
     */
    public function form_chosen($name, $data, $options = array())
    {
        $id_name = 'field-'.$name;

        $is_multiple = false;
        if(array_key_exists('multiple', $options) && $options['multiple']) $is_multiple = true;

        $placeholder = '';
        if(array_key_exists('placeholder', $options)) $placeholder = $options['placeholder']; 

        if($this->is_json($data)) {
            $data = json_decode($data);
        }

        if(!array_key_exists('options', $options) || empty($options['options'])) {
            $options['options'] = array();
        }
            

        $control = '<select '.(($is_multiple) ? 'multiple' : '').' data-placeholder="'.$placeholder.'" id="'.$id_name.'" name="'.$name.''.(($is_multiple) ? '[]' : '').'" class="chosen-select">';
        foreach($options['options'] as $k => $v) {
            $selected = '';
            if(is_array($data) && in_array($k, $data)) {
                $selected = 'selected';
            }
            elseif($k == $data) $selected = 'selected';

            $control .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        $control .= '</select>';

        $control .= '
            <script>
                jQuery(document).ready(function(){
                    jQuery("#'.$id_name.'").chosen({});
                });
            </script>';

        return $control;
    }
    


}