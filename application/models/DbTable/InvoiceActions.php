<?php

class Application_Model_DbTable_InvoiceActions extends Zend_Db_Table_Abstract
{

    protected $_name = 'invoiceAction';

    protected $_referenceMap = array(
            'Invoice' => array(
                    'columns' => 'invoiceId',
                    'refTableClass' => 'Application_Model_DbTable_Invoices',
                    'refColumns' => 'id'));

}
