<?php

/**
 * Invoice Action Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 5794d82f5f1a02136a2c98f20ad41b015c4259f9 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Action Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_InvoiceAction extends My_ModelAbstract
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
     * Action date
     *
     * @var Zend_Date
     */
    protected $_actionDate;

    /**
     * Whodunnit?
     *
     * @var string
     */
    protected $_who;

    /**
     * What action?
     *
     * @var integer
     */
    protected $_what;

    public static $actions = array(
            self::WHAT_CREATED_MANUAL => 'Manual approval invoice created',
            self::WHAT_CREATED_AUTO => 'Auto approval invoice created',
            self::WHAT_APPROVED => 'Invoice approved for shipment',
            self::WHAT_RECEIVED => 'Invoice received by processor',
            self::WHAT_PROCESSED => 'Invoice processed by processor',
            self::WHAT_PAID => 'Invoice marked as PAID',
            self::WHAT_CANCELLED => 'Invoice cancelled',
            self::WHAT_NOTE => 'Note added',
            self::WHAT_INVOICE_NUMBER_UPDATED => 'Invoice Number Updated',);

    const WHAT_CREATED_MANUAL = 1;
    const WHAT_CREATED_AUTO = 2;
    const WHAT_APPROVED = 3;
    const WHAT_RECEIVED = 4;
    const WHAT_PROCESSED = 5;
    const WHAT_PAID = 6;
    const WHAT_CANCELLED = 7;
    const WHAT_NOTE = 8;
    const WHAT_INVOICE_NUMBER_UPDATED = 9;

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
        $this->_invoiceId = $invoiceId;
    }

    /**
     * Get $this->_actionDate
     *
     * @see $_actionDate
     * @return Zend_Date
     */
    public function getActionDate()
    {
        return $this->_actionDate;
    }

    /**
     * Set $this->_actionDate
     *
     * @see $_actionDate
     * @param $actionDate Zend_Date|DateTime|string
     */
    public function setActionDate($actionDate)
    {
        if ($actionDate instanceof Zend_Date) {
            // do nothing, we are already correct
        } elseif ($actionDate instanceof DateTime) {
            /* @var $actionDate DateTime */
            $actionDate = new Zend_Date($actionDate->format(DateTime::ISO8601),
                    Zend_Date::ISO_8601);
        } else {
            try {
                $actionDate = new Zend_Date((string) $actionDate, Zend_Date::ISO_8601);
            } catch (Exception $e) {
                throw new Zend_Exception('Date could not be parsed.');
            }
        }
        /* @var $actionDate Zend_Date */
        $this->_actionDate = $actionDate;
    }

    /**
     * Set who
     *
     * @return string
     */
    public function getWho()
    {
        return $this->_who;
    }

    /**
     * Get who
     *
     * @param string $who
     * @return Application_Model_InvoiceAction
     */
    public function setWho($who)
    {
        $this->_who = $who;
        return $this;
    }

    /**
     * Get what
     *
     * @return integer
     */
    public function getWhat()
    {
        return $this->_what;
    }

    /**
     * Set what
     *
     * @param integer $what
     * @return Application_Model_InvoiceAction
     */
    public function setWhat($what)
    {
        $this->_what = (int) $what;
        return $this;
    }

}
