<?php

class Application_Form_Register extends Application_Form_Seller
{

    public function init()
    {
        parent::init();
        $this->getElement('name')->setAttrib('disabled', 'disabled');
        $this->getElement('contactName')->setAttrib('disabled', 'disabled');
        $this->getElement('contactEmail')->setAttrib('disabled', 'disabled');
    }

}
