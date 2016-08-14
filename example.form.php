<?php

include_once dirname(__FILE__).'/plugme/plugme.form.php';

/**
 * Movie form
 */
class example_form extends plugme_form
{
    protected $form_fields = array(

        'name' => array(
            'type'     => 'text',
            'label'    => 'Full name',
            'required' => true,
            'description' => 'Example of description for "text" input element.',
        ),
        'enabled' => array(
            'type'     => 'switch',
            'label'    => 'Enabled',
            'required' => false,
            'description' => 'Example of description for "switch" element (css checkbox).',
        ),
        // 'gender' => array(
        //     'type'     => 'select',
        //     'label'    => 'Gender',
        //     'required' => false,
        //     'options'  => array(
        //         'male'    => 'Male',
        //         'female'  => 'Female',
        //         'other'   => 'Other',
        //         'unknown' => 'Unknown'
        //     ),
        //     'description' => 'Example of description for "select" element.',
        // ),
        'dateofbirth' => array(
            'type'     => 'datepicker',
            'label'    => 'Date of birth',
            'required' => false,
            'default'  => '',
            'description' => 'Example of description for "datepicker" element. (use jquery ui)'
        ),
        'country' => array(
            'type'     => 'chosen',
            'label'    => 'Country',
            'required' => false,
            'default'  => 'US', //@see init()
            'multiple' => false,
            'description' => 'Example of description for "chosen" element. (use chosen js)',
        ),
        'biography' => array(
            'type'     => 'texteditor',
            'label'    => 'Biography',
            'required' => false,
            'description' => 'Example of description for "texteditor" element. (use wp_editor)',
        ),
        'profile_image' => array(
            'type'     => 'image',
            'label'    => 'Profile image',
            'required' => false,
            'description' => 'Example of description for "image" element. (use wp_media)',
        ),
        'occupation' => array(
            'type'     => 'textarea',
            'label'    => 'Occupation',
            'required' => false,
            
        ),

        'slider' => array(
            'type'     => 'slider',
            'label'    => 'Quote',
            'required' => false,
            
        ),
    );

    public function init()
    {
       
        $this->form_fields['country']['options'] = $this->get_country_list();
            
    }

    public function pre_save($data)
    {
        if(!array_key_exists('enabled', $data)) $data['enabled'] = 0;

        // echo '<pre>';
        // print_r($_POST);
        // print_r($data['directors']); exit();

        return $data;
    }


}