<?php
namespace Label\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class LabelFormValidator implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    public $data;
    
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter)
        {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $id = $this->data['id'];
            if (!$id) {
                $id = null;
            }

            $inputFilter->add($factory->createInput([
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => '5',
                            'max' => '45',
                        ),
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9_]+$/',
                            'messages' => array(
                                'regexNotMatch' => 'Please use only letters and numbers',
                            ),
                        ),
                    ),
                    array(
                        'name' => '\Zend\Validator\Db\NoRecordExists',
                        'options' => array(
                            'table' => 'ht_label',
                            'field' => 'name',
                            'adapter' => $this->dbAdapter,
                            'exclude' => $id,
                            'messages' => array(
                                \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'Label name is already defined',
                            ),
                        ),
                    ),
                ),
            ]));

            $inputFilter->add($factory->createInput([
                'name' => 'default_text',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => '1',
                            'max' => '1000',
                        ),
                    ),
                ),
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}