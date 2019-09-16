<?php

class Application_Form_AdminUser extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();
        $this->addElement('Select', 'type', array(
                'label' => 'Account Type',
                'required' => true,
                'class' => 'input-xlarge',
                'multiOptions' => Application_Model_AdminUser::$types,
                ));
        $this->addElement('Text', 'name', array(
                'label' => 'Company Name',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => 'Example Corp.',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        ),
                ));
        $this->addElement('Text', 'contactName', array(
                'label' => 'Contact Name',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => 'John Doe',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        ),
                ));
        $this->addElement('Text', 'contactEmail', array(
                'label' => 'Contact Email',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => 'j.doe@example.com',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        'EmailAddress',
                        array('Db_NoRecordExists', null, array(
                                'table' => 'adminUser',
                                'field' => 'contactEmail',
                                'messages' => array(
                                        'recordFound' => "An account for '%value%' already exists.",
                                ),
                                )),
                        ),
                ));
    }

}
