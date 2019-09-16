<?php

class Application_Model_DbTable_BuyListItems extends Zend_Db_Table_Abstract
{

    protected $_name = 'buyListItem';

    protected $_referenceMap = array(
            'BuyList' => array(
                    'columns' => 'buyListId',
                    'refTableClass' => 'Application_Model_DbTable_BuyLists',
                    'refColumns' => 'id'));

}
