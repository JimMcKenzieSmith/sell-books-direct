<?php

/**
 * Seller Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: f13ebf6c01ed537711519f7d5e92ea06841b78d8 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Seller Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_SellerMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_Sellers';

    public function fetchAll()
    {
        $results = $this->getDbTable()->fetchAll();
        if (count($results) < 1) {
            return array();
        }
        $sellers = array();
        foreach ($results as $row) {
            $sellers[] = $this->modelFromRow($row);
        }
        return $sellers;
    }

    public function fetchAllActive()
    {
        $select = $this->getDbTable()->select()
            ->where('sellerStatus = 2 OR sellerStatus = 3')
            ->where('emailNotify = 1');
        $results = $this->getDbTable()->fetchAll($select);
        if (count($results) < 1) {
            return array();
        }
        $sellers = array();
        foreach ($results as $row) {
            $sellers[] = $this->modelFromRow($row);
        }
        return $sellers;
    }

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

    public function searchByName($term)
    {
        $results = $this->getDbTable()->fetchAll(array('name LIKE ?' => '%'.$term.'%'));
        if (count($results) < 1) {
            return array();
        }
        $sellers = array();
        foreach ($results as $row) {
            $sellers[] = $this->modelFromRow($row);
        }
        return $sellers;
    }

    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        return new Application_Model_Seller($row->toArray());
    }

    public function save(Application_Model_Seller $seller)
    {
        $data = array(
                'id' => $seller->getId(),
                'name' => $seller->getName(),
                'contactName' => $seller->getContactName(),
                'contactEmail' => $seller->getContactEmail(),
                'contactPhone' => $seller->getContactPhone(),
                'payeeName' => $seller->getPayeeName(),
                'paymentAddress1' => $seller->getPaymentAddress1(),
                'paymentAddress2' => $seller->getPaymentAddress2(),
                'paymentCity' => $seller->getPaymentCity(),
                'paymentState' => $seller->getPaymentState(),
                'paymentZip' => $seller->getPaymentZip(),
                'passwordHash' => $seller->getPasswordHash(),
                'passwordSalt' => $seller->getPasswordSalt(),
                'passwordChange' => $seller->getPasswordChange(),
                'sellerStatus' => $seller->getSellerStatus(),
                'emailNotify' => $seller->getEmailNotify(),
                'isClickwrap' => $seller->getIsClickwrap(),
                'clickwrapId' => $seller->getClickwrapId(),
                );
        if (is_null($seller->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $seller->setId($id);
            return $id;
        } else {
            return $this->getDbTable()->update($data, array('id = ?' => $seller->getId()));
        }
    }

}
