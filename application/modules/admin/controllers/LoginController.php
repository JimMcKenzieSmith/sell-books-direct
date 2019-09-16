<?php

/**
 * Login Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 68b9d57c7dd52dcfdb230b5d92e0ef075a229bbb $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Login Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Admin_LoginController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_forward('login');
    }

    public function loginAction()
    {
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $auth = Zend_Auth::getInstance();
                $adapter = $this->getInvokeArg('bootstrap')->getResource('adminAuthAdapter');
                /* @var $adapter Zend_Auth_Adapter_DbTable */
                $adapter->setIdentity($form->username->getValue());
                $adapter->setCredential(hash("sha256", $this->getInvokeArg('bootstrap')->getOption('passwordSalt') . $form->password->getValue()));
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    $row = $adapter->getResultRowObject('id');
                    $adminUserMapper = new Application_Model_AdminUserMapper();
                    $adminUser = $adminUserMapper->find($row->id);
                    if (!isset($adminUser)) {
                        $auth->clearIdentity();
                        $this->view->error = array("Your Username and/or Password was not recognized.");
                    } else {
                        $namespace = new Zend_Session_Namespace('User');
                        $namespace->user = $adminUser;
                        $superAdmins = explode(',', $this->getInvokeArg('bootstrap')->getOption('superAdmins'));
                        if (in_array($adminUser->getId(), $superAdmins)) {
                            $namespace->role = 'superAdmin';
                        } else {
                            $namespace->role = $adminUser->getRoleId();
                        }
                        $redirect = array('module'=>'admin','controller'=>'index','action'=>'index');
                        $this->_helper->redirector->gotoRouteAndExit($redirect);
                    }
                } else {
                    $this->view->error = array("Your Username and/or Password was not recognized.");
                }
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_helper->redirector('login','login','admin');
    }

}
