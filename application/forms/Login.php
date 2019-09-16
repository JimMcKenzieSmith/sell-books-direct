<?php

class Application_Form_Login extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();
        $this->addElement('Text', 'username', array(
                'required' => true,
                'label' => 'Email Address',
                'placeholder' => 'email@example.com',
                'validators' => array(
                        'EmailAddress',
                        ),
                ));
        $this->addElement('Password', 'password', array(
                'required' => true,
                'label' => 'Password'
                ));
    }

    public function isValid($data, $forgotPasswordClicked=false)
    {
        if($forgotPasswordClicked) {
            $this->getElement('password')->setRequired(false);
        }
        return parent::isValid($data);
    }

}
