<?php

class Application_Model_DbTable_Invoices extends Zend_Db_Table_Abstract
{

    protected $_name = 'invoice';

    protected $_dependentTables = array(
            'Application_Model_DbTable_InvoiceItems',
            'Application_Model_DbTable_InvoiceNotes',
            'Application_Model_DbTable_InvoiceActions',
            );

}
