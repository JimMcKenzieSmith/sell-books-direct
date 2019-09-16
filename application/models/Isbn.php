<?php

/**
 * ISBN Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 430d48e180ebab879b017ff23c018674729184ed $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * ISBN Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_Isbn
{

    /**
     * ISBN13
     *
     * @var string
     */
    protected $_isbn13 = null;

    /**
     * ISBN10
     *
     * @var string
     */
    protected $_isbn10 = null;

    /**
     * ISBN validator, lazy loaded with getValidator()
     *
     * @see getValidator()
     * @var null Zend_Validate_Isbn
     */
    protected $_validator = null;

    /**
     * Create a new ISBN model
     *
     * @param $isbn string
     * @param $format string
     *            Either Zend_Validate_Isbn::ISBN13, Zend_Validate_Isbn::ISBN10
     *            or Zend_Validate_Isbn::AUTO
     * @throws Zend_Exception
     */
    public function __construct($isbn, $format = Zend_Validate_Isbn::AUTO)
    {
        $filter = new Zend_Filter_Alnum();
        $isbn = $filter->filter($isbn);
        $this->getValidator()->setType($format);
        if (!$this->getValidator()->isValid($isbn)) {
            throw new Zend_Exception('Not a valid ISBN');
        }
        switch (strlen($isbn)) {
            case 13:
                $this->_isbn13 = $isbn;
                break;
            case 10:
                $this->_isbn10 = $isbn;
                $this->_isbn13 = $this->convert10to13($this->_isbn10);
                break;
            default:
                throw new Zend_Exception('Not a valid ISBN');
                break;
        }
    }

    /**
     * Convert legacy ISBN10 to new ISBN13
     *
     * @param $isbn10 string
     *            ISBN10 value
     * @throws Zend_Exception
     * @return string
     */
    protected function convert10to13($isbn10)
    {
        $this->getValidator()->setType(Zend_Validate_Isbn::ISBN10);
        if (!$this->getValidator()->isValid($isbn10)) {
            throw new Zend_Exception('Not a valid ISBN10');
        }
        $isbn13 = '978' . substr($isbn10, 0, 9);
        $sum = 0;
        for ($i = 0; $i < 12; $i ++) {
            if ($i % 2 == 0) {
                $sum += $isbn13{$i};
            } else {
                $sum += 3 * $isbn13{$i};
            }
        }
        $checksum = 10 - ($sum % 10);
        if ($checksum == 10) {
            $checksum = '0';
        }
        $isbn13 = $isbn13 . $checksum;
        $this->getValidator()->setType(Zend_Validate_Isbn::ISBN13);
        if (!$this->getValidator()->isValid($isbn13)) {
            throw new Zend_Exception('ISBN10 to ISBN13 Conversion failed.');
        }
        $this->_isbn13 = $isbn13;
        return $this->_isbn13;
    }

    /**
     * Get validator
     *
     * @return Zend_Validate_Isbn
     */
    protected function getValidator()
    {
        if (is_null($this->_validator)) {
            $this->_validator = new Zend_Validate_Isbn();
        }
        return $this->_validator;
    }

    /**
     * Get ISBN13 value
     *
     * @throws Zend_Exception
     * @return string
     */
    protected function getIsbn13()
    {
        if (is_null($this->_isbn13)) {
            throw new Zend_Exception('ISBN not set');
        }
        return $this->_isbn13;
    }

    /**
     * Get current ISBN13 as an ISBN10
     *
     * @return string
     */
    public function getIsbn10()
    {
        if (substr($this->toString(),0,3) !== '978') {
            $isbn10 = 'N/A';
        } else {
            $isbn10 = substr($this->toString(),3,9);
            $checksum = 0;
            $weight = 10;
            $isbnCharArray = str_split($isbn10);
            foreach($isbnCharArray as $char) {
                $checksum += $char * $weight;
                $weight--;
            }
            $checksum = 11-($checksum % 11);
            if ($checksum == 10) {
                $isbn10 .= "X";
            } elseif ($checksum == 11) {
                $isbn10 .= "0";
            } else {
                $isbn10 .= $checksum;
            }
        }
        return (string) $isbn10;
    }

    /**
     * Get as string
     */
    public function toString()
    {
        return (string) $this->getIsbn13();
    }

    /**
     * Magic function hook
     */
    public function __toString()
    {
        return (string) $this->toString();
    }
}
