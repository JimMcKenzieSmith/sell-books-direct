<?php

/**
 * Invoice Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 0c63e100c0d4c31eaec9349caba1a671fc530c27 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_Invoice extends My_ModelAbstract implements
        Zend_Acl_Resource_Interface
{

    /**
     * Internal ID
     *
     * @var string
     */
    protected $_id;

    /**
     * Seller Internal ID
     *
     * @var int
     */
    protected $_sellerId;

    /**
     * Seller provided invoice number
     *
     * @var string
     */
    protected $_sellerInvoiceNumber;


    /**
     * Expected ship date
     *
     * @var Zend_Date
     */
    protected $_shipDate;

    /**
     * Created timestamp
     *
     * @var Zend_Date
     */
    protected $_createTs;

    /**
     * Invoice Status
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    protected $_invoiceStatus;

    /**
     * Invoice line items
     *
     * @var array[ISBN13=>Application_Model_InvoiceItem]
     */
    protected $_lineItems = array();

    /**
     * Invoice Total Price
     *
     * @var float
     */
    protected $_totalPrice;

    /**
     * Invoice Total Lines
     *
     * @var int
     */
    protected $_totalLines;

    /**
     * Invoice Total Items
     *
     * @var int
     */
    protected $_totalItems;

    /**
     * Internal Buy List ID
     *
     * @var int
     */
    protected $_buyListId;

    protected $_actions = array();

    protected $_notes = array();

    /**
     * Seller Status Types
     *
     * @var array
     */
    public static $_invoiceStatusTypes = array(
            self::STATUS_PENDING_APPROVAL => 'Awaiting manual approval',
            self::STATUS_APPROVED => 'Approved for shipment',
            self::STATUS_RECEIVED => 'Received by Processor',
            self::STATUS_PROCESSED => 'Processed by Processor',
            self::STATUS_PAID => 'PAID',
            self::STATUS_CANCELLED => 'CANCELLED');

    public static $shortStatusTypes = array(
            self::STATUS_QUOTE => 'Quote',
            self::STATUS_PENDING_APPROVAL => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled'
            );

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_QUOTE = 0;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_PENDING_APPROVAL = 1;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_APPROVED = 2;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_RECEIVED = 3;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_PROCESSED = 4;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_PAID = 5;

    /**
     *
     * @see $_invoiceStatusTypes
     * @var int
     */
    const STATUS_CANCELLED = 6;

    /**
     * Get $this->_id
     *
     * @see $_id
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set $this->_id
     *
     * @see $_id
     * @param $id string
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        foreach ($this->_lineItems as $item) {
            /* @var $item Application_Model_InvoiceItem */
            $item->setInvoiceId($id);
        }
    }

    /**
     * Get $this->_sellerId
     *
     * @see $_sellerId
     * @return int
     */
    public function getSellerId()
    {
        return $this->_sellerId;
    }

    /**
     *
     * @return bool
     */
    public function isMkzInvoice()
    {
        return $this->getSellerId()==Application_Model_Seller::SELLER_MKZBOOKS;
    }

    /**
     * Set $this->_sellerId
     *
     * @see $_sellerId
     * @param $sellerId int
     */
    public function setSellerId($sellerId)
    {
        if (!is_numeric($sellerId)) {
            throw new Zend_Exception(
                    'Seller ID must be a number or a numeric string');
        }
        $this->_sellerId = (int) $sellerId;
    }

    /**
     * Get $this->_sellerInvoiceNumber
     *
     * @see $_sellerInvoiceNumber
     * @return string
     */
    public function getSellerInvoiceNumber()
    {
        return $this->_sellerInvoiceNumber;
    }

    /**
     * Set $this->_sellerInvoiceNumber
     *
     * @see $_sellerInvoiceNumber
     * @param $sellerInvoiceNumber string
     */
    public function setSellerInvoiceNumber($sellerInvoiceNumber)
    {
        $validator = new Zend_Validate_Regex('/[a-zA-Z0-9\-]*/i');
        if (!$validator->isValid($sellerInvoiceNumber)) {
            throw new Zend_Exception(
                    'Seller Invoice Number must be alphanumeric,' .
                             ' and may contain dashes.');
        }
        $this->_sellerInvoiceNumber = (string) strtoupper($sellerInvoiceNumber);
    }

    /**
     * Get $this->_createTs
     *
     * @see $_createTs
     * @return Zend_Date
     */
    public function getCreateTs()
    {
        return $this->_createTs;
    }

    /**
     * Set $this->_createTs
     *
     * @see $_createTs
     * @param $createTs Zend_Date|DateTime|string
     */
    public function setCreateTs($createTs)
    {
        if ($createTs instanceof Zend_Date) {
            // do nothing, we are already correct
        } elseif ($createTs instanceof DateTime) {
            /* @var $createTs DateTime */
            $createTs = new Zend_Date($createTs->format(DateTime::ISO8601),
                Zend_Date::ISO_8601);
        } else {
            try {
                $createTs = new Zend_Date((string) $createTs, Zend_Date::ISO_8601);
            } catch (Exception $e) {
                throw new Zend_Exception('Date could not be parsed.');
            }
        }
        /* @var $createTs Zend_Date */
        $this->_createTs = $createTs;
    }

    /**
     * Get $this->_shipDate
     *
     * @see $_shipDate
     * @return Zend_Date
     */
    public function getShipDate()
    {
        return $this->_shipDate;
    }

    /**
     * Set $this->_shipDate
     *
     * @see $_shipDate
     * @param $shipDate Zend_Date|DateTime|string
     */
    public function setShipDate($shipDate)
    {
        if ($shipDate instanceof Zend_Date) {
            // do nothing, we are already correct
        } elseif ($shipDate instanceof DateTime) {
            /* @var $shipDate DateTime */
            $shipDate = new Zend_Date($shipDate->format(DateTime::ISO8601),
                    Zend_Date::ISO_8601);
        } else {
            try {
                $shipDate = new Zend_Date((string) $shipDate, Zend_Date::ISO_8601);
            } catch (Exception $e) {
                throw new Zend_Exception('Date could not be parsed.');
            }
        }
        /* @var $shipDate Zend_Date */
        $this->_shipDate = $shipDate;
    }

    /**
     * Get $this->_invoiceStatus
     *
     * @see $_invoiceStatus
     * @return int
     */
    public function getInvoiceStatus()
    {
        return $this->_invoiceStatus;
    }

    /**
     * Get $_invoiceStatusTypes[statusId]
     *
     * @param $invoiceStatusId
     * @return string
     */
    public function getInvoiceStatusAsText($invoiceStatusId)
    {
        return self::$_invoiceStatusTypes[$invoiceStatusId];
    }

    /**
     * Set $this->_invoiceStatus
     *
     * @see $_invoiceStatus
     * @param $invoiceStatus int
     *            {@see $_invoiceStatusTypes}
     */
    public function setInvoiceStatus($invoiceStatus)
    {
        if (!isset(self::$shortStatusTypes[$invoiceStatus])) {
            throw new Zend_Exception('Invoice status must be predefined.');
        }
        $this->_invoiceStatus = $invoiceStatus;
    }

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

    public function getBuyListName()
    {
        $buyListMapper = new Application_Model_BuyListMapper();
        return $buyListMapper->findNoItems($this->getBuyListId())->getName();
    }

    /**
     * Set $this->_buyListId
     *
     * @see $_buyListId
     * @param $buyListId int
     */
    public function setBuyListId($buyListId)
    {
        if (!is_numeric($buyListId)) {
            throw new Zend_Exception(
                    'Buy List ID must be a number or a numeric string');
        }
        $this->_buyListId = (int) $buyListId;
    }

    /**
     * Return the ACL resource ID applicable to this invoice
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->getId();
    }

    /**
     * Get line items on invoice
     *
     * @return array[ISBN13=>Application_Model_InvoiceItem]
     */
    public function getLineItems()
    {
        return $this->_lineItems;
    }

    public function setLineItems($lineItems)
    {
        if (!is_array($lineItems)) {
            throw new Zend_Exception('Must be an array');
        }
        $this->_lineItems = array();
        foreach ($lineItems as $item) {
            if (is_array($item)) {
                $item = new Application_Model_InvoiceItem($item);
            }
            if (!$item instanceof Application_Model_InvoiceItem) {
                throw new Zend_Exception('Array must contain InvoiceItems or nested arrays that can be resolved to InvoiceItems.');
            }
            $this->addLineItem($item);
        }
    }

    /**
     * Add new line item to invoice
     *
     * @param $item Application_Model_InvoiceItem
     * @internal Resets the total* temp variables
     */
    public function addLineItem(Application_Model_InvoiceItem $item)
    {
        $this->_totalPrice = null;
        $this->_totalLines = null;
        $this->_totalItems = null;
        $item->setInvoiceId($this->getId());
        $this->_lineItems[$item->getIsbn13()] = $item;
    }

    /**
     * Get invoice total
     *
     * @return float
     */
    public function getTotalPrice()
    {
        if (is_null($this->_totalPrice)) {
            $total = 0.00;
            foreach ($this->getLineItems() as $lineItem) {
                /* @var $lineItem Application_Model_InvoiceItem */
                $total += $lineItem->getTotal();
            }
            $this->_totalPrice = $total;
        }
        return $this->_totalPrice;
    }

    /**
     * Get total lines on invoice
     *
     * @return int
     */
    public function getTotalLines()
    {
        if (is_null($this->_totalLines)) {
            $this->_totalLines = count($this->_lineItems);
        }
        return $this->_totalLines;
    }

    /**
     * Get total quantity of lines on invoice
     *
     * @return int
     */
    public function getTotalItems()
    {
        if (is_null($this->_totalItems)) {
            $items = 0;
            foreach ($this->getLineItems() as $lineItem) {
                /* @var $lineItem Application_Model_InvoiceItem */
                $items += $lineItem->getQuantity();
            }
            $this->_totalItems = $items;
        }
        return $this->_totalItems;
    }

    public function setActions(array $actions)
    {
        $this->_actions = $actions;
        return $this;
    }

    public function getActions()
    {
        return $this->_actions;
    }

    public function addAction(Application_Model_InvoiceAction $action)
    {
        $action->setInvoiceId($this->getId());
        $this->_actions[] = $action;
        return $this;
    }

    public function setNotes(array $notes)
    {
        $this->_notes = $notes;
        return $this;
    }

    public function getNotes()
    {
        return $this->_notes;
    }

    public function addNote(Application_Model_InvoiceNote $note)
    {
        $note->setInvoiceId($this->getId());
        $this->_notes[] = $note;
        return $this;
    }

    public function getCreatedDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_CREATED_AUTO || $action->getWhat() == Application_Model_InvoiceAction::WHAT_CREATED_MANUAL) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }

    public function getApprovedDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_APPROVED) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }

    public function getReceivedDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_RECEIVED) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }

    public function getProcessedDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_PROCESSED) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }

    public function getPaidDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_PAID) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }

    public function getCancelledDate()
    {
        $actions = array();
        foreach ($this->getActions() as $action) {
            /* @var $action Application_Model_InvoiceAction */
            if ($action->getWhat() == Application_Model_InvoiceAction::WHAT_CANCELLED) {
                $actions[$action->getId()] = $action;
            }
        }
        if (count($actions) < 1) {
            return null;
        }
        krsort($actions);
        $action = reset($actions);
        return $action->getActionDate();
    }



}
