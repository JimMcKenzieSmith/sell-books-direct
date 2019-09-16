<?php

class My_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        switch ($request->getModuleName()) {
            case 'default':
                if ($request->getControllerName() !== 'login') {
                    $auth = Zend_Auth::getInstance();
                    if (!$auth->hasIdentity()) {
                        $redirector = new Zend_Controller_Action_Helper_Redirector();
                        $redirector->gotoSimpleAndExit('login', 'login',
                                'default');
                    }
                    $namespace = new Zend_Session_Namespace('User');
                    if (isset($namespace->user) && $namespace->user instanceof Application_Model_AdminUser) {
                        $flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
                        $flashMessenger->setNamespace('error');
                        $flashMessenger->addMessage('You are attempting to access the non-admin area of the website while logged in as an admin.  Please logout, and then try again.');
                        $redirector = new Zend_Controller_Action_Helper_Redirector();
                        $redirector->gotoSimpleAndExit('index', 'index',
                                'admin');
                    }
                    if ($namespace->user->getIsClickwrap()) {
                        $clickwrapMapper = new Application_Model_ClickwrapMapper();
                        $clickwrap = $clickwrapMapper->findLatest();
                        if (is_null($namespace->user->getClickwrapId()) || $namespace->user->getClickwrapId() !== $clickwrap->getId()) {
                            $redirector = new Zend_Controller_Action_Helper_Redirector();
                            $redirector->gotoSimpleAndExit('clickwrap','login');
                        }
                    }
                    if ($namespace->user->getPasswordChange() === 2) {
                    	$redirector = new Zend_Controller_Action_Helper_Redirector();
                        $redirector->gotoSimpleAndExit('register','login');
                    }
                }
                break;
            case 'admin':
                if ($request->getControllerName() !== 'login') {
                    $auth = Zend_Auth::getInstance();
                    if (!$auth->hasIdentity()) {
                        $redirector = new Zend_Controller_Action_Helper_Redirector();
                        $redirector->gotoSimpleAndExit('login', 'login',
                                'admin');
                    }
                    $namespace = new Zend_Session_Namespace('User');
                    if (isset($namespace->user) && !$namespace->user instanceof Application_Model_AdminUser) {
                        $redirector = new Zend_Controller_Action_Helper_Redirector();
                        $redirector->gotoSimpleAndExit('index', 'index',
                                'default');
                    }
                }
                break;
            default:
                return;
                break;
        }
    }

}
