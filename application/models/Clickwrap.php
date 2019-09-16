<?php

/**
 * Clickwrap Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 0413ee07533c5d73a95454d6db972d1c3084069d $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Clickwrap Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_Clickwrap extends My_ModelAbstract
{

    /**
     * Internal ID
     *
     * @var int
     */
    protected $_id;

    /**
     * Date
     *
     * @var Zend_Date
     */
    protected $_date;

    /**
     * Clickwrap Agreement Text
     *
     * @var string
     */
    protected $_agreement;

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
        return $this;
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
     * Get $this->_agreement
     *
     * @see $_agreement
     * @return string
     */
    public function getAgreement()
    {
        return $this->_agreement;
    }

    /**
     * Set $this->_agreement
     *
     * @see $_agreement
     * @param string $_agreement
     */
    public function setAgreement($agreement)
    {
        $this->_agreement = $agreement;
    }

}
