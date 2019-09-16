<?php

/**
 * Model Mapper base
 *
 * @package SellBooksDirect
 * @subpackage Library
 * @version $Id: 306d166d5253ad643fea0f141af86fa636188ad5 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Common model mapper functionality
 *
 * @package SellBooksDirect
 * @subpackage Library
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
abstract class My_ModelMapperAbstract
{

    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     *
     * @var string
     */
    protected $_dbTableString;

    /**
     * Setup the DbTable
     *
     * @param $dbTable string|Zend_Db_Table_Abstract
     * @throws Exception
     * @return My_ModelMapper
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (! $dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get the stored DbTable for this model.
     * Will attempt to set it if not already done.
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbTableString);
        }
        return $this->_dbTable;
    }

}
