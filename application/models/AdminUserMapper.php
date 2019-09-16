<?php

/**
 * Admin User Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: 1d6da649d3df9c9b3f0b08e7a4003dfb3d8df75b $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Admin User Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_AdminUserMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_AdminUsers';

    public function find($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRow($results->current());
    }

    public function findByEmail($email)
    {
        $row = $this->getDbTable()->fetchRow(array('contactEmail = ?' => $email));
        if (is_null($row)) {
            return null;
        }
        return $this->modelFromRow($row);
    }

    public function fetchAll()
    {
        $results = $this->getDbTable()->fetchAll();
        if (count($results) < 1) {
            return array();
        }
        $users = array();
        foreach ($results as $row) {
            $users[] = $this->modelFromRow($row);
        }
        return $users;
    }

    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        return new Application_Model_AdminUser($row->toArray());
    }

    public function save(Application_Model_AdminUser $adminUser)
    {
        $data = array(
                'id' => $adminUser->getId(),
                'name' => $adminUser->getName(),
                'contactName' => $adminUser->getContactName(),
                'contactEmail' => $adminUser->getContactEmail(),
                'passwordHash' => $adminUser->getPasswordHash(),
                'passwordSalt' => $adminUser->getPasswordSalt(),
                'type' => $adminUser->getType(),
                );
        if (is_null($adminUser->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $adminUser->setId($id);
            return $id;
        } else {
            return $this->getDbTable()->update($data, array('id = ?' => $adminUser->getId()));
        }
    }

}
