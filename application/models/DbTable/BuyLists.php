<?php

class Application_Model_DbTable_BuyLists extends Zend_Db_Table_Abstract
{

    protected $_name = 'buyList';

    protected $_dependentTables = array('Application_Model_DbTable_BuyListItems');

}
