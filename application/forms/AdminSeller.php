<?php

class Application_Form_AdminSeller extends Application_Form_Seller
{

    public function init()
    {
        parent::init();
        $this->addElement('Select', 'sellerStatus', array(
                'label' => 'Seller Status',
                'required' => true,
                'class' => 'input-xlarge',
                'multiOptions' => Application_Model_Seller::$sellerStatusTypes,
                'order' => 0,
                ));
        $this->addElement('Radio', 'isClickwrap', array(
                'label' => 'Use Clickwrap Agreement',
                'required' => true,
                'separator' => '',
                'value' => true,
                'multiOptions' => array(
                        true => 'Yes',
                        false => 'No',
                        ),
                ));
    }

}
