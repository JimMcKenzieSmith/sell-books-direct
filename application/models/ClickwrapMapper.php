<?php

/**
 * Clickwrap Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @version $Id: 4fdd91dacbb80255364fc41375dfa8927ef5978b $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Clickwrap Mapper
 *
 * @package SellBooksDirect
 * @subpackage Mapper
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Application_Model_ClickwrapMapper extends My_ModelMapperAbstract
{

    protected $_dbTableString = 'Application_Model_DbTable_Clickwraps';

    public function find($id)
    {
        $results = $this->getDbTable()->find($id);
        if (count($results) < 1) {
            return null;
        }
        return $this->modelFromRow($results->current());
    }

    public function findLatest()
    {
        $row = $this->getDbTable()->fetchRow(null, 'date DESC');
        if (is_null($row)) {
            return null;
        }
        return $this->modelFromRow($row);
    }

    public function modelFromRow(Zend_Db_Table_Row_Abstract $row)
    {
        $clickwrap = new Application_Model_Clickwrap($row->toArray());
        return $clickwrap;
    }

    public function save(Application_Model_Clickwrap $clickwrap)
    {
        $data = array(
                'id' => $clickwrap->getId(),
                'uploadDate' => $clickwrap->getDate()->toString('y-M-d H:m:s'),
                'agreement' => $clickwrap->getAgreement(),
                );
        if (is_null($clickwrap->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $clickwrap->setId($id);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $clickwrap->getId()));
        }
        return $clickwrap;
    }

}
