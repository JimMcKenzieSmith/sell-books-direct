<?php

/**
 * Invoice Note Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 83e99d1379ef634a7500ffd92ee34cd47c83bf4b $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Note Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_InvoiceNote extends My_ModelAbstract
{

    /**
     * Internal ID
     *
     * @var int
     */
    protected $_id;

    /**
     * Invoice Internal ID
     *
     * @var string
     */
    protected $_invoiceId;

    /**
     * Date
     *
     * @var Zend_Date
     */
    protected $_date;

    /**
     * Note
     *
     * @var string
     */
    protected $_note;

    /**
     * Whodunnit?
     *
     * @var string
     */
    protected $_who;

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
        $this->_id = (int) $id;
    }

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
        $this->_invoiceId = (string) $invoiceId;
    }

    /**
     * Get $this->_date
     *
     * @see $_date
     * @return Zend_Date
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Set $this->_date
     *
     * @see $_date
     * @param $date Zend_Date|DateTime|string
     */
    public function setDate($date)
    {
        if ($date instanceof Zend_Date) {
            // do nothing, we are already correct
        } elseif ($date instanceof DateTime) {
            /* @var $date DateTime */
            $date = new Zend_Date($date->format(DateTime::ISO8601),
                    Zend_Date::ISO_8601);
        } else {
            try {
                $date = new Zend_Date((string) $date, Zend_Date::ISO_8601);
            } catch (Exception $e) {
                throw new Zend_Exception('Date could not be parsed.');
            }
        }
        /* @var $date Zend_Date */
        $this->_date = $date;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->_note;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Application_Model_InvoiceNote
     */
    public function setNote($note)
    {
        $this->_note = (string) $note;
        return $this;
    }

    /**
     * Get who
     *
     * @return string
     */
    public function getWho()
    {
        return $this->_who;
    }

    /**
     * Set who
     *
     * @param string $who
     * @return Application_Model_InvoiceNote
     */
    public function setWho($who)
    {
        $this->_who = $who;
        return $this;
    }

}
