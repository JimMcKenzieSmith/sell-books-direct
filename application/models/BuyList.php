<?php

/**
 * Buy List Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: abf725527276c3ecba3b9790e5ac9536719b25b5 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Buy List Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_BuyList extends My_ModelAbstract
{

    /**
     * Internal ID
     *
     * @var int
     */
    protected $_id;

    /**
     * Upload date
     *
     * @var Zend_Date
     */
    protected $_uploadDate;

    /**
     * Name of the buy list (the buy list ID)
     *
     * @var string
     */
    protected $_name;

    /**
     * Invoice line items
     *
     * @var array[ISBN13=>Application_Model_InvoiceItem]
     */
    protected $_items = array();

    /**
     * Get $this->_id
     *
     * @see $_id
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set $this->_id
     *
     * @see $_id
     * @param $id int
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        foreach ($this->getItems() as $item) {
            $item->setBuyListId($id);
        }
        return $this;
    }

    /**
     * Get $this->_uploadDate
     *
     * @see $_uploadDate
     * @return Zend_Date
     */
    public function getUploadDate()
    {
        return $this->_uploadDate;
    }

    /**
     * Set $this->_uploadDate
     *
     * @see $_uploadDate
     * @param $uploadDate Zend_Date|DateTime|string
     */
    public function setUploadDate($uploadDate)
    {
        if ($uploadDate instanceof Zend_Date) {
            // do nothing, we are already correct
        } elseif ($uploadDate instanceof DateTime) {
            /* @var $uploadDate DateTime */
            $uploadDate = new Zend_Date($uploadDate->format(DateTime::ISO8601),
                    Zend_Date::ISO_8601);
        } else {
            try {
                $uploadDate = new Zend_Date((string) $uploadDate, Zend_Date::ISO_8601);
            } catch (Exception $e) {
                throw new Zend_Exception('Date could not be parsed.');
            }
        }
        /* @var $uploadDate Zend_Date */
        $this->_uploadDate = $uploadDate;
    }

    /**
     * Get items in buy list
     *
     * @return array[ISBN13=>Application_Model_BuyListItem]
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Add new item to buy list
     *
     * @param $item Application_Model_BuyListItem
     * @internal Resets the total* temp variables
     */
    public function addItem(Application_Model_BuyListItem $item)
    {
        $item->setBuyListId($this->getId());
        $this->_items[$item->getIsbn13()] = $item;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

}
