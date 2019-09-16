<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

    public function _initAdminAuthAdapter()
    {
        $this->getApplication()->bootstrap('db');
        $authAdapter = new Zend_Auth_Adapter_DbTable(
                $this->getApplication()->getResource('db'),
                'adminUser',
                'contactEmail',
                'passwordHash',
                'SHA1(CONCAT(?,passwordSalt))'
        );
        $this->getApplication()->getContainer()->adminauthadapter = $authAdapter;
        return $authAdapter;
    }

}
