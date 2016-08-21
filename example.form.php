<?php

include_once dirname(__FILE__).'/plugme/plugme.form.php';

/**
 * Form example
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
                //'if_not_empty', //conditonnal validator
                // 'alphanum' => array(
                //     'options' => array('space' => false, 'punc' => array(',','.','!')),
                //     'msg'     => 'Only alpha numeric value'
                // ),
                
                // 'text' => array(
                //     'msg' => 'Text only',
                // )
                // 'int' => array(
                //     'options' => array('min' => 0, 'max' => 1000),
                //     'msg' => 'Only number between 0 and 1000'
                // ),
                // 'regex' => array(
                //     'options' => '/^[A-Z]+$/',
                //     'msg' => 'Regex A-Z'
                // ),
                // 'float' => array(
                //     'options' => array('min' => 0, 'max' => 1000),
                //     'msg' => 'Only float number between 0 and 1000'
                // )
                'enum' => array(
                    'options' => array('test', 'test1', 'test2'),
                    'msg' => 'Only test, test1 and test2'
                ),
            )
        ),

        'email' => array(
            'type'        => 'input',
            'label'       => 'Email',
            'description' => 'Example of description for "email" input element.',
            'attrs'       => array(
                'required'    => false,
                'placeholder' => 'example@email.com',
                'type'        => 'email'
            ),
        ),

        'password' => array(
            'type'        => 'input',
            'label'       => 'Password',
            'description' => 'Example of description for "password" input element.',
            'attrs'       => array(
                'required'    => false,
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
       
        //$this->form_fields['country']['options'] = $this->get_countries_list();
            
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