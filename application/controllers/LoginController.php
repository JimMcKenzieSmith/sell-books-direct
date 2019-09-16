<?php

/**
 * Login Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 5aa50052b738782213e4e6ff0f1bd0a0db064043 $
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
class LoginController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_forward('login');
    }

    public function loginAction()
    {
        $this->_helper->layout()->setLayout('landing');
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isPost()) {
            if ($request->getParam('forgot') == 'forgot') {
                if (!$form->isValid($request->getParams(), true)) {
                    $this->view->form = $form;
                    return;
                }
                $redirect = array('module'=>'default','controller'=>'login','action'=>'forgot','email'=>$request->getParam('username'));
                $this->_helper->redirector->gotoRouteAndExit($redirect);
            }
            if ($form->isValid($request->getParams())) {
                $auth = Zend_Auth::getInstance();
                $adapter = $this->getInvokeArg('bootstrap')->getResource('authAdapter');
                /* @var $adapter Zend_Auth_Adapter_DbTable */
                $adapter->setIdentity($form->username->getValue());
                $adapter->setCredential(hash("sha256", $this->getInvokeArg('bootstrap')->getOption('passwordSalt') . $form->password->getValue()));
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    $row = $adapter->getResultRowObject('id');
                    $sellerMapper = new Application_Model_SellerMapper();
                    $seller = $sellerMapper->find($row->id);
                    if (!isset($seller)) {
                        $auth->clearIdentity();
                        $this->view->error = array("Your Username and/or Password was not recognized.");
                    } else {
                        $namespace = new Zend_Session_Namespace('User');
                        $namespace->user = $seller;
                        $namespace->role = $seller->getRoleId();
                        if ($seller->getIsClickwrap()) {
                            $clickwrapMapper = new Application_Model_ClickwrapMapper();
                            $clickwrap = $clickwrapMapper->findLatest();
                            if (is_null($seller->getClickwrapId()) || $seller->getClickwrapId() !== $clickwrap->getId()) {
                                $this->_helper->redirector->gotoSimpleAndExit('clickwrap');
                            }
                        }
                        switch ($seller->getPasswordChange()) {
                            case 0:
                                $redirect = array('module'=>'default','controller'=>'index','action'=>'index');
                                break;
                            case 2:
                                $redirect = array('module'=>'default','controller'=>'login','action'=>'register');
                                break;
                            default:
                                $redirect = array('module'=>'default','controller'=>'login','action'=>'change');
                                break;
                        }
                        $this->_helper->redirector->gotoRouteAndExit($redirect);
                    }
                } else {

                    $adminUserMapper = new Application_Model_AdminUserMapper();
                    $adminUser = $adminUserMapper->findByEmail($form->username->getValue());
                    if($adminUser) {
                        $this->view->error = array('You have an ADMIN account. Please login here: <a href="https://www.sellbooksdirect.com/admin">www.sellbooksdirect.com/admin</a>');
                    } else {
                        $this->view->error = array("Your Username and/or Password was not recognized.");
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    public function clickwrapAction()
    {
        $clickwrapMapper = new Application_Model_ClickwrapMapper();
        $clickwrap = $clickwrapMapper->findLatest();
        $this->view->clickwrap = $clickwrap;
        if (!is_null($this->view->user->getClickwrapId())) {
            $this->view->alert = array('The terms and conditions have been updated since you last accessed this service.');
        }
        if ($this->getRequest()->isPost()) {
            if ($this->_getParam('agree')) {
                $sellerMapper = new Application_Model_SellerMapper();
                $this->view->user->setClickwrapId($clickwrap->getId());
                $sellerMapper->save($this->view->user);
                switch ($this->view->user->getPasswordChange()) {
                    case 0:
                        $redirect = array('module'=>'default','controller'=>'index','action'=>'index');
                        break;
                    case 2:
                        $redirect = array('module'=>'default','controller'=>'login','action'=>'register');
                        break;
                    default:
                        $redirect = array('module'=>'default','controller'=>'login','action'=>'change');
                        break;
                }
                $this->_helper->redirector->gotoRouteAndExit($redirect);
            } else {
                $this->view->error = array('You have to agree to the terms and conditions below to use this service.');
            }
        }
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $redirect = array('module'=>'default','controller'=>'login','action'=>'login');
        $this->_redirect($redirect);
    }

    public function changeAction()
    {
        $form = new Application_Form_ChangePassword();
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isPost()) {
            $form->password2->getValidator('Identical')->setToken($this->_getParam('password1'));
            if ($form->isValid($request->getParams())) {
                $sellerMapper = new Application_Model_SellerMapper();
                $this->view->user->changePassword($form->password1->getValue(), $this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
                $this->view->user->setPasswordChange(false);
                $sellerMapper->save($this->view->user);
                $redirect = array('module'=>'default','controller'=>'index','action'=>'index');
                $this->_helper->redirector->gotoRouteAndExit($redirect);
            }
        }
        $this->view->form = $form;
    }

    public function forgotAction()
    {
        $this->_helper->layout()->setLayout('landing');

        if (is_null($this->_getParam('email'))) {
            $redirect = array('module'=>'default','controller'=>'login','action'=>'login');
            $this->_helper->redirector->gotoRouteAndExit($redirect);
        }
        $sellerMapper = new Application_Model_SellerMapper();
        $seller = $sellerMapper->findByEmail($this->_getParam('email'));
        if (!is_null($seller)) {
            $temp = $seller->temporaryPassword($this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
            $sellerMapper->save($seller);
            $view = new Zend_View();
            $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
            $view->contactName = $seller->getContactName();
            $view->contactEmail = $seller->getContactEmail();
            $view->temp = $temp;
            $mail = new Zend_Mail();
            $mail->addTo($seller->getContactEmail(), $seller->getContactName());
            $mail->setSubject('Password Reset Request');
            $mail->setBodyText($view->render('forgot.phtml'));
            $mail->send();
        }
        $this->view->email = $this->_getParam('email');
    }

    public function registerAction()
    {
        $form = new Application_Form_Register();
        $preregister = array(
                'id' => $this->view->user->getId(),
                'name' => $this->view->user->getName(),
                'contactName' => $this->view->user->getContactName(),
                'contactEmail' => $this->view->user->getContactEmail(),
                );
        $form->populate($preregister);
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        $request->setParams($preregister);
        if ($request->isPost()) {
            if ($form->isValid($request->getParams())) {
                $sellerMapper = new Application_Model_SellerMapper();
                $this->view->user->exchangeArray($form->getValues());
                $this->view->user->setPasswordChange(true);
                $sellerMapper->save($this->view->user);
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->sellerId = $this->view->user->getId();
                $view->companyName = $this->view->user->getName();
                $mail = new Zend_Mail();
                $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                $mail->setSubject('Seller Account Change: ' . $this->view->user->getId());
                $mail->setBodyText($view->render('seller.phtml'));
                $mail->send();
                $redirect = array('module'=>'default','controller'=>'login','action'=>'change');
                $this->_helper->redirector->gotoRouteAndExit($redirect);
            } else {
                foreach ($form->getElements() as $element) {
                    /* @var $element Zend_Form_Element */
                    if ($element->hasErrors()) {
                        $element->setAttrib('placeholder', null);
                    }
                }
            }
        }
        $this->view->form = $form;
    }

}
