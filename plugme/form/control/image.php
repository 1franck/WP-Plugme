<?php

/**
 * Image uploading (use native wp_media)
 */
class plugme_form_control_image extends plugme_form_control
{
    /**
     * Default options
     * @var array
     */
    protected $default_options = array(
        'label'       => '',
        'description' => '',  
        'attrs' => array(
            'required'    => false,
            'placeholder' => '',
        ),
    );

    /**
     * Generated the control
     * 
     * @return string
     */
    public function generate()
    {
        $attrs = $this->attributes();
        
        if(empty($this->data)) $no_image = true;

        $id_name = $this->attrs_array['id'];

        $image_url = site_url().'/'.$this->data;

        $control = '
            <input type="text" '.$attrs.' class="regular-text">
            <input type="button" name="upload-btn" id="upload-btn-'.$id_name.'" class="button-secondary" value="'.__('Upload image').'">';

        if(!isset($no_image)) {
            $control .= '<br><img id="image-'.$id_name.'" src="'.$image_url .'" width="300px"><br>';
        }
        else {
            $control .= '<br><img id="image-'.$id_name.'" src="'.$image_url .'" width="0px"><br>';
        }         

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
                            $("#image-'.$id_name.'").attr({
                                "src" : real_image_url,
                                "width" : "300px"
                            });
                        });
                    });
                });
            </script>';    

        wp_enqueue_media();
        return $control;
    }
}