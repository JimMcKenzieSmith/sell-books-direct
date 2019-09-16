<?php

/**
 * Buy List Item Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 75100c2cf57a7ef718fbeed16fb7318dac74edd5 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Buy List Item Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_BuyListItem extends My_ModelAbstract
{

    /**
     * Invoice Internal ID
     *
     * @var int
     */
    protected $_buyListId;

    /**
     * List item ISBN13
     *
     * @var Application_Model_Isbn
     */
    protected $_isbn13;

    /**
     * List item quantity
     *
     * @var int
     */
    protected $_quantity;

    /**
     * List item price individual
     *
     * @var float
     */
    protected $_price;

    /**
     * Get $this->_buyListId
     *
     * @see $_buyListId
     * @return int
     */
    public function getBuyListId()
    {
        return $this->_buyListId;
    }

    /**
     * Set $this->_buyListId
     *
     * @see $_buyListId
     * @param $buyListId int
     */
    public function setBuyListId($buyListId)
    {
        $this->_buyListId = (int) $buyListId;
    }

    /**
     * Get $this->_isbn13 as string
     *
     * @see $_isbn13
     * @return string
     */
    public function getIsbn13()
    {
        return $this->_isbn13->toString();
    }

    /**
     * Get $this->_isbn13
     *
     * @see $_isbn13
     * @return Application_Model_Isbn
     */
    public function getIsbn13Raw()
    {
        return $this->_isbn13;
    }

    /**
     * Set $this->_isbn13
     *
     * @see $_isbn13
     * @param $isbn13 string
     */
    public function setIsbn13($isbn10or13)
    {
        $this->_isbn13 = new Application_Model_Isbn($isbn10or13);
    }

    /**
     * Set $this->_isbn13
     *
     * @see $_isbn13
     * @param $isbn Application_Model_Isbn
     */
    public function setIsbn13Raw(Application_Model_Isbn $isbn)
    {
        $this->_isbn13 = $isbn;
    }

    /**
     * Get $this->_quantity
     *
     * @see $_quantity
     * @return number
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * Set $this->_quantity
     *
     * @see $_quantity
     * @param $quantity number
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = (int) $quantity;
    }

    /**
     * Get $this->_price
     *
     * @see $_price
     * @return number
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * Set $this->_price
     *
     * @see $_price
     * @param $price number
     */
    public function setPrice($price)
    {
        $this->_price = (float) $price;
    }

}
