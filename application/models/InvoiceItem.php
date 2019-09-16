<?php

/**
 * Invoice Item Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 39445b69c80ae792bf4f04ba6d4ec062135fcb21 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Item Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_InvoiceItem extends My_ModelAbstract
{

    /**
     * Invoice Internal ID
     *
     * @var string
     */
    protected $_invoiceId;

    /**
     * Line item ISBN13
     *
     * @var Application_Model_Isbn
     */
    protected $_isbn13;

    /**
     * Line item quantity
     *
     * @var int
     */
    protected $_quantity;

    /**
     * Line item price individual
     *
     * @var float
     */
    protected $_price;

    /**
     * Line item total
     *
     * @var float
     */
    protected $_total;

    /**
     * Get $this->_invoiceId
     *
     * @see $_invoiceId
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->_invoiceId;
    }

    /**
     * Set $this->_invoiceId
     *
     * @see $_invoiceId
     * @param $invoiceId string
     */
    public function setInvoiceId($invoiceId)
    {
        $this->_invoiceId = $invoiceId;
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
        $this->_total = null;
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
        $this->_total = null;
    }

    /**
     * Line total
     *
     * @throws Zend_Exception
     * @return float
     */
    public function getTotal()
    {
        if (!is_numeric($this->getQuantity()) || !is_numeric($this->getPrice())) {
            throw new Zend_Exception(
                    'Cannot get line total without quantity and price');
        }
        if (is_null($this->_total)) {
            $this->_total = $this->getPrice() * $this->getQuantity();
        }
        return $this->_total;
    }

}
