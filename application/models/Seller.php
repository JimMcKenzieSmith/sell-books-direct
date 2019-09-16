<?php

/**
 * Seller Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id: 5bc31e42be27888cb12f2c72e059c9439ac7f135 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Seller Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_Seller extends My_ModelAbstract implements
        Zend_Acl_Role_Interface
{
    CONST SELLER_MKZBOOKS = 3;

    /**
     * Internal ID
     *
     * @var int
     */
    protected $_id;

    /**
     * Company Name
     *
     * @var string
     */
    protected $_name;

    /**
     * Contact Name
     *
     * @var string
     */
    protected $_contactName;

    /**
     * Contact Email
     *
     * @var string
     */
    protected $_contactEmail;

    /**
     * Contact Phone
     *
     * @var string
     */
    protected $_contactPhone;

    /**
     * Payee Name
     *
     * @var string
     */
    protected $_payeeName;

    /**
     * Payment Address 1
     *
     * @var string
     */
    protected $_paymentAddress1;

    /**
     * Payment Address 2
     *
     * @var string
     */
    protected $_paymentAddress2;

    /**
     * Payment City
     *
     * @var string
     */
    protected $_paymentCity;

    /**
     * Payment State
     *
     * @var string
     */
    protected $_paymentState;

    /**
     * Payment Zip code
     *
     * @var string
     */
    protected $_paymentZip;

    /**
     * Password Hash
     *
     * @var string
     */
    protected $_passwordHash;

    /**
     * Password Salt
     *
     * @var string
     */
    protected $_passwordSalt;

    /**
     * Password change required
     *
     * @var bool
     */
    protected $_passwordChange;

    /**
     * Seller Account Status
     *
     * @see $_sellerStatusTypes
     * @var int
     */
    protected $_sellerStatus;

    /**
     * Optin for Email notifications
     *
     * @var boolean
     */
    protected $_emailNotify;

    /**
     * Must this seller agree to clickwrap agreement?
     *
     * @var boolean
     */
    protected $_isClickwrap;

    /**
     * Clickwrap agreement linked to seller
     *
     * @var integer
     */
    protected $_clickwrapId;

    /**
     * Seller Status Types
     *
     * @var array
     */
    public static $sellerStatusTypes = array(
            self::STATUS_UNACTIVATED => 'Unactivated',
            self::STATUS_ACTIVATED_MANUAL => 'Activated (Manual Approval)',
            self::STATUS_ACTIVATED_AUTO => 'Activated (Auto Approval)',
            self::STATUS_DEACTIVATED => 'Deactivated');

    /**
     * Seller ACL Roles
     *
     * @var array
     */
    protected static $_aclRoles = array(
            self::STATUS_UNACTIVATED => 'unactivated',
            self::STATUS_ACTIVATED_MANUAL => 'activatedManual',
            self::STATUS_ACTIVATED_AUTO => 'activatedAuto',
            self::STATUS_DEACTIVATED => 'deactivated');

    /**
     *
     * @see $_sellerStatusTypes
     * @var int
     */
    const STATUS_UNACTIVATED = 1;

    /**
     *
     * @see $_sellerStatusTypes
     * @var int
     */
    const STATUS_ACTIVATED_MANUAL = 2;

    /**
     *
     * @see $_sellerStatusTypes
     * @var int
     */
    const STATUS_ACTIVATED_AUTO = 3;

    /**
     *
     * @see $_sellerStatusTypes
     * @var int
     */
    const STATUS_DEACTIVATED = 4;

    /**
     * Get $this->_id
     *
     * @see $_id
     * @return number
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set $this->_id
     *
     * @see $_id
     * @param $id number
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * Get $this->_name
     *
     * @see $_name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set $this->_name
     *
     * @see $_name
     * @param $name string
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get $this->_contactName
     *
     * @see $_contactName
     * @return string
     */
    public function getContactName()
    {
        return $this->_contactName;
    }

    /**
     * Set $this->_contactName
     *
     * @see $_contactName
     * @param $contactName string
     */
    public function setContactName($contactName)
    {
        $this->_contactName = $contactName;
    }

    /**
     * Get $this->_contactEmail
     *
     * @see $_contactEmail
     * @return string
     */
    public function getContactEmail()
    {
        return $this->_contactEmail;
    }

    /**
     * Set $this->_contactEmail
     *
     * @see $_contactEmail
     * @param $contactEmail string
     */
    public function setContactEmail($contactEmail)
    {
        $this->_contactEmail = $contactEmail;
    }

    /**
     * Get $this->_contactPhone
     *
     * @see $_contactPhone
     * @return string
     */
    public function getContactPhone()
    {
        return $this->_contactPhone;
    }

    /**
     * Set $this->_contactPhone
     *
     * @see $_contactPhone
     * @param $contactPhone string
     */
    public function setContactPhone($contactPhone)
    {
        $this->_contactPhone = $contactPhone;
    }

    /**
     * Get $this->_payeeName
     *
     * @see $_payeeName
     * @return string
     */
    public function getPayeeName()
    {
        return $this->_payeeName;
    }

    /**
     * Set $this->_payeeName
     *
     * @see $_payeeName
     * @param $payeeName string
     */
    public function setPayeeName($payeeName)
    {
        $this->_payeeName = $payeeName;
    }

    /**
     * Get $this->_paymentAddress1
     *
     * @see $_paymentAddress1
     * @return string
     */
    public function getPaymentAddress1()
    {
        return $this->_paymentAddress1;
    }

    /**
     * Set $this->_paymentAddress1
     *
     * @see $_paymentAddress1
     * @param $paymentAddress1 string
     */
    public function setPaymentAddress1($paymentAddress1)
    {
        $this->_paymentAddress1 = $paymentAddress1;
    }

    /**
     * Get $this->_paymentAddress2
     *
     * @see $_paymentAddress2
     * @return string
     */
    public function getPaymentAddress2()
    {
        return $this->_paymentAddress2;
    }

    /**
     * Set $this->_paymentAddress2
     *
     * @see $_paymentAddress2
     * @param $paymentAddress2 string
     */
    public function setPaymentAddress2($paymentAddress2)
    {
        $this->_paymentAddress2 = $paymentAddress2;
    }

    /**
     * Get $this->_paymentCity
     *
     * @see $_paymentCity
     * @return string
     */
    public function getPaymentCity()
    {
        return $this->_paymentCity;
    }

    /**
     * Set $this->_paymentCity
     *
     * @see $_paymentCity
     * @param $paymentCity string
     */
    public function setPaymentCity($paymentCity)
    {
        $this->_paymentCity = $paymentCity;
    }

    /**
     * Get $this->_paymentState
     *
     * @see $_paymentState
     * @return string
     */
    public function getPaymentState()
    {
        return $this->_paymentState;
    }

    /**
     * Set $this->_paymentState
     *
     * @see $_paymentState
     * @param $paymentState string
     */
    public function setPaymentState($paymentState)
    {
        $this->_paymentState = $paymentState;
    }

    /**
     * Get $this->_paymentZip
     *
     * @see $_paymentZip
     * @return string
     */
    public function getPaymentZip()
    {
        return $this->_paymentZip;
    }

    /**
     * Set $this->_paymentZip
     *
     * @see $_paymentZip
     * @param $paymentZip string
     */
    public function setPaymentZip($paymentZip)
    {
        $this->_paymentZip = $paymentZip;
    }

    /**
     * Get $this->_passwordChange
     *
     * @see $_passwordChange
     * @return boolean
     */
    public function getPasswordChange()
    {
        return $this->_passwordChange;
    }

    /**
     * Return the ACL role applicable to this seller
     *
     * @see $_aclRoles
     * @return string
     */
    public function getRoleId()
    {
        return self::$_aclRoles[$this->_sellerStatus];
    }

    /**
     * Get $this->_passwordHash
     *
     * @see $_passwordHash
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->_passwordHash;
    }

    /**
     * Set $this->_passwordHash
     *
     * @see $_passwordHash
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->_passwordHash = $passwordHash;
    }

    /**
     * Get $this->_passwordSalt
     *
     * @see $_passwordSalt
     * @return string
     */
    public function getPasswordSalt()
    {
        return $this->_passwordSalt;
    }

    /**
     * Set $this->_passwordSalt
     *
     * @see $_passwordSalt
     * @param string $passwordSalt
     */
    public function setPasswordSalt($passwordSalt)
    {
        $this->_passwordSalt = $passwordSalt;
    }

    /**
     * Get $this->_sellerStatus
     *
     * @see $_sellerStatus
     * @return integer
     */
    public function getSellerStatus()
    {
        return $this->_sellerStatus;
    }

    /**
     * Set $this->_sellerStatus
     *
     * @see $_sellerStatus
     * @param integer $sellerStatus
     */
    public function setSellerStatus($sellerStatus)
    {
        $this->_sellerStatus = $sellerStatus;
    }

    /**
     * Set $this->_passwordChange
     *
     * @see $_passwordChange
     * @param boolean $passwordChange
     */
    public function setPasswordChange($passwordChange)
    {
        $this->_passwordChange = $passwordChange;
    }

    public function changePassword($newPassword, $applicationSalt)
    {
        $userSalt = '';
        for ($i = 0; $i < 64; $i++) {
            $userSalt .= chr(rand(33, 126));
        }
        $passwordHash = sha1(hash("sha256", $applicationSalt . $newPassword) . $userSalt);
        $this->setPasswordHash($passwordHash);
        $this->setPasswordSalt($userSalt);
    }

    public function temporaryPassword($applicationSalt)
    {
        $password = '';
        for ($i = 0; $i < 9; $i++) {
            $password .= chr(rand(33, 126));
        }
        $this->changePassword($password, $applicationSalt);
        $this->setPasswordChange(true);
        return $password;
    }

    /**
     * Get email notify
     *
     * @return boolean
     */
    public function getEmailNotify()
    {
        return (bool) $this->_emailNotify;
    }

    /**
     * Set email notify
     *
     * @param boolean $notify
     * @return Application_Model_Seller
     */
    public function setEmailNotify($notify)
    {
        $this->_emailNotify = (bool) $notify;
        return $this;
    }

    /**
     * Get $this->_isClickwrap
     *
     * @see $_isClickwrap
     * @return boolean
     */
    public function getIsClickwrap()
    {
        return $this->_isClickwrap;
    }

     /**
     * Set $this->_isClickwrap
     *
     * @see $_isClickwrap
     * @param boolean $_isClickwrap
     */
    public function setIsClickwrap($isClickwrap)
    {
        $this->_isClickwrap = (bool) $isClickwrap;
    }

    /**
     * Get $this->_clickwrapId
     *
     * @see $_clickwrapId
     * @return number
     */
    public function getClickwrapId()
    {
        return $this->_clickwrapId;
    }

    /**
     * Set $this->_clickwrapId
     *
     * @see $_clickwrapId
     * @param number $_clickwrapId
     */
    public function setClickwrapId($clickwrapId)
    {
        $this->_clickwrapId = $clickwrapId;
    }


}
