<?php
namespace Label\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class TranslationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'trans_id',
                'id' => 'trans_id',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'label_id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'label_id',
                'id' => 'label_id',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'language',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'language',
                'id' => 'language',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'text',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Translation',
            ),
        ));
        
        $this->add(array(
            'name' => 'send',
            'type'  => 'Submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => 'Submit',
                'id' => 'submit',
            ),
        ));

    }
}