<?php

/**
 * Buy List Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: 25059d43302fde689fdb28b1411607ce7d758536 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Buy List Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_BuyListMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_BuyLists';

    public function find($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRow($results->current());
    }

    /**
     * "No items" just means don't load all the line items (faster).
     *
     * @param $id
     * @return Application_Model_BuyList|null
     */
    public function findNoItems($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRowNoItems($results->current());
    }

    public function findLatest()
    {
        $row = $this->getDbTable()->fetchRow(null, 'id DESC');
        if (is_null($row)) {
            return null;
        }
        return $this->modelFromRow($row);
    }

    /**
     * @return Application_Model_BuyList|null
     */
    public function findLatestHeaders()
    {
        $row = $this->getDbTable()->fetchRow(null, 'id DESC');
        if (is_null($row)) {
            return null;
        }
        return $this->modelHeadersFromRow($row);
    }

    public function modelHeadersFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        $buyList = new Application_Model_BuyList($row->toArray());
        return $buyList;
    }

    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        $buyList = new Application_Model_BuyList($row->toArray());
        $buyListItemTable = new Application_Model_DbTable_BuyListItems();
        $itemRows = $row->findDependentRowset($buyListItemTable);
        foreach ($itemRows->toArray() as $item) {
            $itemModel = new Application_Model_BuyListItem($item);
            $buyList->addItem($itemModel);
        }
        return $buyList;
    }

    /**
     * "No items" just means don't load all the line items (faster).
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return Application_Model_BuyList
     */
    public function modelFromRowNoItems(Zend_Db_Table_Row_Abstract $row)
    {
        $buyList = new Application_Model_BuyList($row->toArray());

        return $buyList;
    }

    public function save(Application_Model_BuyList $buyList)
    {
        $data = array(
                'id' => $buyList->getId(),
                'uploadDate' => $buyList->getUploadDate()->toString('y-M-d H:m:s'),
                'name' => $buyList->getName(),
                );
        $buyListItemTable = new Application_Model_DbTable_BuyListItems();
        if (is_null($buyList->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $buyList->setId($id);
            foreach ($buyList->getItems() as $item) {
                /* @var $item Application_Model_BuyListItem */
                $data = array(
                        'buyListId' => $item->getBuyListId(),
                        'isbn13' => $item->getIsbn13(),
                        'quantity' => $item->getQuantity(),
                        'price' => $item->getPrice(),
                        );
                $buyListItemTable->insert($data);
            }
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $buyList->getId()));
            foreach ($buyList->getItems() as $item) {
                /* @var $item Application_Model_BuyListItem */
                $data = array(
                        'buyListId' => $item->getBuyListId(),
                        'isbn13' => $item->getIsbn13(),
                        'quantity' => $item->getQuantity(),
                        'price' => $item->getPrice(),
                        );
                $buyListItemTable->update($data, array('buyListId = ?' => $data['buyListId'], 'isbn13 = ?' => $data['isbn13']));
            }
        }
        return $buyList;
    }

}
