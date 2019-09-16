<?php

class Application_Model_DbTable_InvoiceItems extends Zend_Db_Table_Abstract
{

    protected $_name = 'invoiceItem';

    protected $_referenceMap = array(
            'Invoice' => array(
                    'columns' => 'invoiceId',
                    'refTableClass' => 'Application_Model_DbTable_Invoices',
                    'refColumns' => 'id'));

}
