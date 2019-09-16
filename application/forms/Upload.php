<?php

class Application_Form_Upload extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();
        $this->addElement('File', 'file', array(
                'required' => true,
                'label' => 'List File (.csv)',
                'decorators' => array(
                    'Label',
                    'File',
                    'Errors',
                    'ControlGroup',
                        ),
                'validators' => array(
                        array('Count', null, 1),
                        array('Extension', null, 'csv'),
                        ),
                ));
    }

}
