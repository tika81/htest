<?php
namespace Label\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class LabelForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'label_id',
                'id' => 'label_id',
            ),
            'options' => array(
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'name',
                'placeholder' => 'Insert label name here',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'default_text',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'default_text',
                'placeholder' => 'Insert default text here',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Default text',
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