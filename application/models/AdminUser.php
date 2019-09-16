<?php

/**
 * Admin User Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @version $Id $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Admin User Model
 *
 * @package SellBooksDirect
 * @subpackage Model
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_AdminUser extends My_ModelAbstract implements
        Zend_Acl_Role_Interface
{

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
     * Admin Account Type
     *
     * @var int
     */
    protected $_type;

    /**
     * Admin User ACL Roles
     *
     * @var array
     */
    protected static $_aclRoles = array(
            self::TYPE_PROCESSOR => 'processor',
            self::TYPE_BUYER => 'buyer',
            );

    public static $types = array(
            self::TYPE_PROCESSOR => 'Processor',
            self::TYPE_BUYER => 'Buyer',
            );

    const TYPE_PROCESSOR = 1;
    const TYPE_BUYER = 2;

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
     * Set type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get type
     *
     * @param integer $type
     * @return Application_Model_AdminUser
     */
    public function setType($type)
    {
        $this->_type = (int) $type;
        return $this;
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
     * Return the ACL role applicable to this seller
     *
     * @see $_aclRoles
     * @return string
     */
    public function getRoleId()
    {
        return self::$_aclRoles[$this->_type];
    }

    public function changePassword($newPassword, $applicationSalt)
    {
        $userSalt = '';
        for ($i = 0; $i < 64; $i++) {
            $userSalt .= chr(rand(33, 126));
        }
        $passwordHash = sha1(hash("sha256", $applicationSalt . $newPassword) . $userSalt);
        $this->_passwordHash = $passwordHash;
        $this->_passwordSalt = $userSalt;
    }

}
