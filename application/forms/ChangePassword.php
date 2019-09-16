<?php

class Application_Form_ChangePassword extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();
        $this->addElement('Password', 'password1', array(
                'required' => true,
                'label' => 'New Password',
                ));
        $this->addElement('Password', 'password2', array(
                'required' => true,
                'label' => 'Confirm New Password',
                'validators' => array(
                        'Identical',
                        ),
                ));
    }

}
