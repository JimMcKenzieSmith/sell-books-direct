<?php

/**
 * Seller Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: a223a79d4bb22221088142655abf5cb72a8a0d2e $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Seller Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_VCurrentBuyListItemMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_VCurrentBuyListItems';

    public function fetchAll()
    {
        $results = $this->getDbTable()->fetchAll();
        if (count($results) < 1) {
            return array();
        }
        $buyListItems = array();
        foreach ($results as $row) {
            $buyListItems[] = $this->modelFromRow($row);
        }
        return $buyListItems;
    }


    public function find($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRow($results->current());
    }


    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        return new Application_Model_VCurrentBuyListItem($row->toArray());
    }

    public function save(Application_Model_VCurrentBuyListItem $buyListItem)
    {

    }

}
