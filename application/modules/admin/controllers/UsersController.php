<?php

/**
 * Index Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 9652715d23a1bc778555a7c9b9e37b53c0e6a76a $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Index Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Admin_UsersController extends Zend_Controller_Action
{

    /**
     * Index action placeholder
     */
    public function indexAction()
    {
        $this->_helper->redirector('list');
    }

    public function listAction()
    {
        $adminUserMapper = new Application_Model_AdminUserMapper();
        $users = $adminUserMapper->fetchAll();
        $this->view->users = $users;
    }

    public function addAction()
    {
        $form = new Application_Form_AdminUser();
        $form->addElement('Password', 'password1', array(
                'label' => 'Password',
                'required' => true,
                'class' => 'input-xlarge',
                ));
        $form->addElement('Password', 'password2', array(
                'label' => 'Confirm Password',
                'required' => true,
                'class' => 'input-xlarge',
                'validators' => array(
                        'Identical',
                        ),
                ));
        if ($this->getRequest()->isPost()) {
            $password1 = $this->_getParam('password1');
            $form->password2->getValidator('Identical')->setToken($password1);
            if ($form->isValid($this->getRequest()->getPost())) {
                $data = $form->getValues();
                $user = new Application_Model_AdminUser($data);
                $user->changePassword($this->_getParam('password1'), $this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
                $adminUserMapper = new Application_Model_AdminUserMapper();
                $adminUserMapper->save($user);
                $this->_helper->redirector('view', null, null, array('id'=>$user->getId()));
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

    public function viewAction()
    {
        if (is_null(($id = $this->_getParam('id')))) {
            $this->_helper->redirector('list');
        }
        $adminUserMapper = new Application_Model_AdminUserMapper();
        $user = $adminUserMapper->find($id);
        if (is_null($user)) {
            $this->_helper->redirector('list');
        }
        $this->view->user = $user;
    }

    public function editAction()
    {
        if (is_null(($id = $this->_getParam('id')))) {
            $this->_helper->redirector('list');
        }
        $adminUserMapper = new Application_Model_AdminUserMapper();
        $user = $adminUserMapper->find($id);
        if (is_null($user)) {
            $this->_helper->redirector('list');
        }
        $form = new Application_Form_AdminUser();
        $form->getElement('contactEmail')->getValidator('Db_NoRecordExists')->setExclude(array(
                'field' => 'id',
                'value' => $user->getId(),
        ));
        $form->populate(array(
                'name' => $user->getName(),
                'contactName' => $user->getContactName(),
                'contactEmail' => $user->getContactEmail(),
                'type' => $user->getType(),
                ));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->view->success = array('Details updated successfully.');
                $user->exchangeArray($form->getValues());
                if (!is_null($this->_getParam('changePassword'))) {
                    $user->changePassword($this->_getParam('changePassword'), $this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
                }
                $adminUserMapper->save($user);
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

    public function updateAction()
    {
        $adminUserMapper = new Application_Model_AdminUserMapper();
        $user = $adminUserMapper->find($this->view->user->getId());

        $form = new Application_Form_AdminUser();
        $form->getElement('contactEmail')->getValidator('Db_NoRecordExists')->setExclude(array(
            'field' => 'id',
            'value' => $user->getId(),
        ));
        $form->getElement('type')->setAttrib('disabled', 'disabled'); // don't let the user change what type of account they have
        $form->populate(array(
            'name' => $user->getName(),
            'contactName' => $user->getContactName(),
            'contactEmail' => $user->getContactEmail(),
            'type' => $user->getType(),
        ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $preregister = array(
                'type' => $user->getType()
            );
            $request->setParams($preregister);  // add the type back, since it is disabled above, and not included in the form submission
            if ($form->isValid($request->getParams())) {
                $this->view->success = array('Details updated successfully.');
                $user->exchangeArray($form->getValues());
                if (!is_null($this->_getParam('changePassword'))) {
                    $user->changePassword($this->_getParam('changePassword'), $this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
                }
                $adminUserMapper->save($user);
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
