<?php

/**
 * Sellers Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 757ca731a36bd7ef288385435ae1d8de1549c46c $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Sellers Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Admin_SellersController extends Zend_Controller_Action
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
        $sellerMapper = new Application_Model_SellerMapper();
        $this->view->sellers = $sellerMapper->fetchAll();
    }

    public function viewAction()
    {
        $sellerMapper = new Application_Model_SellerMapper();
        if (is_null($seller = $sellerMapper->find($this->_getParam('id')))) {
            $this->_helper->redirector('list');
        }
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoices = $invoiceMapper->fetchAllBySeller($seller->getId());
        $this->view->seller = $seller;
        $this->view->invoices = $invoices;
    }

    public function editAction()
    {
        $sellerMapper = new Application_Model_SellerMapper();
        if (is_null($seller = $sellerMapper->find($this->_getParam('id')))) {
            $this->_helper->redirector('list');
        }
        $form = new Application_Form_AdminSeller();

        $form->populate(array(
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
                'sellerStatus' => $seller->getSellerStatus(),
                'emailNotify' => $seller->getEmailNotify(),
                'isClickwrap' => $seller->getIsClickwrap(),
                ));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->view->success = array('Details updated successfully.');
                $seller->exchangeArray($form->getValues());
                $sellerMapper->save($seller);
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->sellerId = $seller->getId();
                $view->companyName = $seller->getName();
                $mail = new Zend_Mail();
                $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                $mail->setSubject('Seller Account Change: ' . $seller->getId());
                $mail->setBodyText($view->render('seller.phtml'));
                $mail->send();
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

    public function addAction()
    {
        $form = new Application_Form_AdminSeller();
        $form->setElements(array(
                 'sellerStatus' => $form->sellerStatus,
                 'name' => $form->name,
                 'contactName' => $form->contactName,
                 'contactEmail' => $form->contactEmail,
                 'isClickwrap' => $form->isClickwrap,
                ));
        $form->sellerStatus->setMultiOptions(array(
                Application_Model_Seller::STATUS_ACTIVATED_MANUAL => Application_Model_Seller::$sellerStatusTypes[Application_Model_Seller::STATUS_ACTIVATED_MANUAL],
                Application_Model_Seller::STATUS_ACTIVATED_AUTO => Application_Model_Seller::$sellerStatusTypes[Application_Model_Seller::STATUS_ACTIVATED_AUTO],
                ));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $sellerMapper = new Application_Model_SellerMapper();
                $seller = new Application_Model_Seller($form->getValues());
                $temp = $seller->temporaryPassword($this->getInvokeArg('bootstrap')->getOption('passwordSalt'));
                $seller->setPasswordChange(2);
                $sellerMapper->save($seller);
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->contactName = $seller->getContactName();
                $view->contactEmail = $seller->getContactEmail();
                $view->temp = $temp;
                $mail = new Zend_Mail();
                $mail->addTo($seller->getContactEmail(), $seller->getContactName());
                $mail->setSubject('New account on Sell Books Direct');
                $mail->setBodyText($view->render('create.phtml'));
                $mail->send();
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->sellerId = $seller->getId();
                $view->companyName = $seller->getName();
                $mail = new Zend_Mail();
                $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                $mail->setSubject('New Seller Account: ' . $seller->getId());
                $mail->setBodyText($view->render('seller.phtml'));
                $mail->send();
                $this->_helper->redirector('view',null,null,array('id'=>$seller->getId()));
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

    public function searchAction()
    {
        if (is_null(($term = $this->_getParam('search')))) {
            $this->_helper->redirector('list');
        }
        $sellerMapper = new Application_Model_SellerMapper();
        $sellers = $sellerMapper->searchByName($term);
        $this->view->term = $term;
        $this->view->sellers = $sellers;
    }

}
