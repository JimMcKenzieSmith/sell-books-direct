<?php

class Application_Form_Generate extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();
        $this->addElement('Text', 'sellerInvoiceNumber', array(
                'label' => 'Invoice Number',
                'required' => true,
                'validators' => array(
                        array('Regex', null, array(
                                'pattern' => "/^[0-9a-zA-Z\\-]+$/",
                                'messages' => array(
                                        'regexNotMatch' => "Please correct. '%value%' must be alpha-numeric, and can contain dashes.",
                                ),
                                )),
                        array('Db_NoRecordExists', null, array(
                                'table' => 'invoice',
                                'field' => 'sellerInvoiceNumber',
                                'messages' => array(
                                        'recordFound' => "Invoice number '%value%' was already used. Please enter a different invoice number.",
                                ),
                                )),
                        ),
                ));
        $this->addElement('Text', 'shipDate', array(
                'label' => 'Ship Date',
                'required' => true,
                'validators' => array(
                        array('Date', null, 'M/d/y'),
                        ),
                ));
    }

}
