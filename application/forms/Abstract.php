<?php

abstract class Application_Form_Abstract extends Zend_Form
{

    public function init()
    {
        $this->addPrefixPath('My_Form_Decorator_', 'My/Form/Decorator/', 'decorator');
        $formDecorators = array(
                'FormElements',
        );
        $this->setDecorators($formDecorators);
        $elementDecorators = array(
                'Label',
                'ViewHelper',
                'Errors',
                'ControlGroup',
        );
        $this->setElementDecorators($elementDecorators);
    }

}
