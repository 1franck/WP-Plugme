<?php

include_once dirname(__FILE__).'/plugme/plugme.form.php';

/**
 * Movie form
 */
class example_form extends plugme_form
{
    protected $form_fields = array(

        'name' => array(
            'type'        => 'input',
            'label'       => 'Full name',
            'description' => 'Example of description for "text" input element.',
            'attrs'       => array(
                'required'    => true,
                'placeholder' => 'Full name',
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
        ),

        'password' => array(
            'type'        => 'input',
            'label'       => 'Password',
            'description' => 'Example of description for "password" input element.',
            'attrs'       => array(
                'required'    => true,
                'type'        => 'password'
            ),
        ),

        'enabled' => array(
            'type'        => 'switch',
            'label'       => 'Enabled',
            'checkbox_label' => 'This is the checkbox label text, yeah, for real!',
            'description' => 'Example of description for "switch" element (css checkbox).',
        ),

        'featured' => array(
            'type'        => 'checkbox',
            'label'       => 'Featured',
            'checkbox_label' => 'This is the checkbox label text, yeah, for real!',
            'description' => 'Example of description for "checkbox" element (css checkbox).',
        ),

        'gender' => array(
            'type'        => 'select',
            'label'       => 'Gender',
            'description' => 'Example of description for "select" element.',
            'attrs'       => array(
                'multiple' => false
            ),
            'options'     => array(
                'male'    => 'Male',
                'female'  => 'Female',
                'other'   => 'Other',
                'unknown' => 'Unknown'
            ),
        ),

        'dateofbirth' => array(
            'type'        => 'datepicker',
            'label'       => 'Date of birth',
            'default'     => '',
            'description' => 'Example of description for "datepicker" element. (use jquery ui)'
        ),

        'country' => array(
            'type'        => 'chosen',
            'label'       => 'Country',
            'default'     => 'US', //@see init()
            'description' => 'Example of description for "chosen" element. (use chosen js)',
            'attrs'       => array(
                'multiple' => false,
                'required' => false,
            ),
        ),

        'bio' => array(
            'type'        => 'texteditor',
            'label'       => 'Biography',
            'description' => 'Example of description for "texteditor" element. (use wp_editor)',
        ),

        'image' => array(
            'type'        => 'image',
            'label'       => 'Profile image',
            'description' => 'Example of description for "image" element. (use wp_media)',
        ),

        'occupation' => array(
            'type'        => 'textarea',
            'label'       => 'Occupation',
            'description' => 'Example of description for "texarea" element.',
            'attrs'       => array(
                'row'      => 4,
            ),
            
        ),

        'note' => array(
            'type'        => 'slider',
            'label'       => 'Note',
            'description' => 'Example of description for "slider" element. (use jquery ui)',
           
        ),

        'role' => array(
            'type'        => 'radio',
            'options'     => array(
                'admin'  => 'Administrator',
                'member' => 'Member',
                'vip'    => 'V.I.P'
            ),
            'label'       => 'Role',
            'description' => 'Example of description for "radio" element.',
           
        ),
    );

    public function init()
    {
       
        $this->form_fields['country']['options'] = $this->get_countries_list();
            
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