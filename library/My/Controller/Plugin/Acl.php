<?php

class My_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getModuleName() == "api") {
            return;
        }
        $acl = Zend_Registry::get('acl');
        /* @var $acl Zend_Acl */
        $resource = 'mvc:' . $request->getModuleName() . '.' .
                 $request->getControllerName() . '.' . $request->getActionName();
        if (!$acl->isAllowed(Zend_Registry::get('role'), $resource)) {
            echo "You are not allowed access to this page.";
            exit;
        }
    }

}
