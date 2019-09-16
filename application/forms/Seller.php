<?php

class Application_Form_Seller extends Application_Form_Abstract
{

    public function init()
    {
        parent::init();

        $this->addElement('hidden', 'id');

        $this->addElement('Text', 'name', array(
                'label' => 'Company Name',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => '',
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
                'placeholder' => '',
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
                'placeholder' => '',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        'EmailAddress',
                        array('Db_NoRecordExists', null, array(
                                'table' => 'seller',
                                'field' => 'contactEmail',
                                'messages' => array(
                                        'recordFound' => "An account for '%value%' already exists.",
                                ),
                                )),
                        ),
                ));
        $this->addElement('Text', 'contactPhone', array(
                'label' => 'Contact Phone',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => '555-555-5555',
                'validators' => array(
                         array('StringLength', array(
                                'max' => 255,
                                )),
                        ),
                ));
        $this->addElement('Text', 'payeeName', array(
                'label' => 'Payee Name',
                'required' => false,
                'class' => 'input-xlarge',
                'placeholder' => '(if different than company name)',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        ),
                ));
        $this->addElement('Text', 'paymentAddress1', array(
                'label' => 'Payment Address 1',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => '',
                ));
        $this->addElement('Text', 'paymentAddress2', array(
                'label' => 'Payment Address 2',
                'required' => false,
                'class' => 'input-xlarge',
                'placeholder' => '(optional)',
                ));
        $this->addElement('Text', 'paymentCity', array(
                'label' => 'Payment City',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => '',
                'validators' => array(
                        array('StringLength', array(
                                'max' => 255,
                                )),
                        ),
                ));
        $this->addElement('Select', 'paymentState', array(
                'label' => 'Payment State',
                'required' => true,
                'class' => 'input-xlarge',
                'multiOptions' => array(
                        '' => 'Select a State',
                        'AL' => 'Alabama',
                        'AK' => 'Alaska',
                        'AZ' => 'Arizona',
                        'AR' => 'Arkansas',
                        'CA' => 'California',
                        'CO' => 'Colorado',
                        'CT' => 'Connecticut',
                        'DE' => 'Delaware',
                        'DC' => 'District Of Columbia',
                        'FL' => 'Florida',
                        'GA' => 'Georgia',
                        'HI' => 'Hawaii',
                        'ID' => 'Idaho',
                        'IL' => 'Illinois',
                        'IN' => 'Indiana',
                        'IA' => 'Iowa',
                        'KS' => 'Kansas',
                        'KY' => 'Kentucky',
                        'LA' => 'Louisiana',
                        'ME' => 'Maine',
                        'MD' => 'Maryland',
                        'MA' => 'Massachusetts',
                        'MI' => 'Michigan',
                        'MN' => 'Minnesota',
                        'MS' => 'Mississippi',
                        'MO' => 'Missouri',
                        'MT' => 'Montana',
                        'NE' => 'Nebraska',
                        'NV' => 'Nevada',
                        'NH' => 'New Hampshire',
                        'NJ' => 'New Jersey',
                        'NM' => 'New Mexico',
                        'NY' => 'New York',
                        'NC' => 'North Carolina',
                        'ND' => 'North Dakota',
                        'OH' => 'Ohio',
                        'OK' => 'Oklahoma',
                        'OR' => 'Oregon',
                        'PA' => 'Pennsylvania',
                        'RI' => 'Rhode Island',
                        'SC' => 'South Carolina',
                        'SD' => 'South Dakota',
                        'TN' => 'Tennessee',
                        'TX' => 'Texas',
                        'UT' => 'Utah',
                        'VT' => 'Vermont',
                        'VA' => 'Virginia',
                        'WA' => 'Washington',
                        'WV' => 'West Virginia',
                        'WI' => 'Wisconsin',
                        'WY' => 'Wyoming'
                        ),
                ));
        $this->addElement('Text', 'paymentZip', array(
                'label' => 'Payment Zip Code',
                'required' => true,
                'class' => 'input-xlarge',
                'placeholder' => '',
                'validators' => array(
                        array('Regex', null, array(
                                'pattern' => '/^\d{5}-?(\d{4})?$/',
                                'messages' => array(
                                        'regexNotMatch' => 'Not a valid Zip code in the format 12345 or 12345-6789.',
                                        ),
                                )),
                        array('StringLength', array(
                                'max' => 10,
                                )),
                        ),
                ));
        $this->addElement('Radio', 'emailNotify', array(
                'label' => 'Receive notifications of new buy lists',
                'required' => true,
                'class' => 'input-xlarge',
                'separator' => '',
                'value' => true,
                'multiOptions' => array(
                        true => 'Yes',
                        false => 'No'),
                ));
    }

    public function isValid($data)
    {
        $id = $this->getValue('id');
        if(!empty($id)) {
            $this->getElement('contactEmail')->getValidator('Db_NoRecordExists')->setExclude(array(
                'field' => 'id',
                'value' => $id,
            ));
        }
        return parent::isValid($data);
    }

}
