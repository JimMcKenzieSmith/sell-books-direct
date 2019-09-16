<?php

/**
 * Invoice Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: 62e06a9e22c90bc936c18e6145183a31ab722414 $
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */

/**
 * Invoice Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @author Michael Gooden (michael@bluepointweb.com)
 * @copyright Copyright (c) 2012 McKenzie Books, Inc. All rights reserved
 *            (http://www.mckenzieservices.com/)
 */
class Admin_InvoicesController extends Zend_Controller_Action
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
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoices = $invoiceMapper->fetchAllCreatedPastNDays(90);
        $sellerMapper = new Application_Model_SellerMapper();
        $sellers = array();
        foreach ($invoices as $invoice) {
            if (!isset($sellers[$invoice->getSellerId()])) {
                $sellers[$invoice->getSellerId()] = $sellerMapper->find($invoice->getSellerId());
            }
        }
        $this->view->sellers = $sellers;
        $this->view->invoices = $invoices;
    }

    public function viewAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }
        if ($this->getRequest()->isPost()) {
            switch ($this->_getParam('submit')) {
                case 'status':
                    if (isset($invoice::$_invoiceStatusTypes[$this->getRequest()->getPost('invoiceStatus')])) {
                        $invoice->setInvoiceStatus($this->getRequest()->getPost('invoiceStatus'));
                        $action = new Application_Model_InvoiceAction();
                        $action->setActionDate(Zend_Date::now());
                        switch ($invoice->getInvoiceStatus()) {
                            case $invoice::STATUS_APPROVED:
                                $what = $action::WHAT_APPROVED;
                                $sellerMapper = new Application_Model_SellerMapper();
                                $seller = $sellerMapper->find($invoice->getSellerId());
                                $view = new Zend_View();
                                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                                $view->contactName = $seller->getContactName();
                                $view->sellerInvoiceNumber = $invoice->getSellerInvoiceNumber();
                                $mail = new Zend_Mail();
                                $mail->addTo($seller->getContactEmail(), $seller->getContactName());
                                $mail->setSubject('Invoice #' . $invoice->getSellerInvoiceNumber() . ' Approved for Shipment');
                                $mail->setBodyText($view->render('approved.phtml'));
                                $mail->send();
                                break;
                            case $invoice::STATUS_RECEIVED:
                                $what = $action::WHAT_RECEIVED;
                                break;
                            case $invoice::STATUS_PROCESSED:
                                $what = $action::WHAT_PROCESSED;
                                break;
                            case $invoice::STATUS_PAID:
                                $what = $action::WHAT_PAID;
                                break;
                            case $invoice::STATUS_CANCELLED:
                                $what = $action::WHAT_CANCELLED;
                                break;
                        }
                        if (isset($what)) {
                            $action->setWhat($what);
                            $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                            $invoice->addAction($action);
                        }
                        $invoiceMapper->save($invoice);
                        $this->view->success = array('Invoice status changed successfully.');
                    }
                    break;
                case 'note':
                    $note = new Application_Model_InvoiceNote();
                    $note->setDate(Zend_Date::now());
                    $note->setNote($this->getRequest()->getPost('note'));
                    $note->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                    $invoice->addNote($note);
                    $action = new Application_Model_InvoiceAction();
                    $action->setActionDate(Zend_Date::now());
                    $action->setWhat($action::WHAT_NOTE);
                    $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                    $invoice->addAction($action);
                    $invoiceMapper->save($invoice);
                    $this->view->success = array('Note added to invoice successfully.');
                    break;
            }
        }
        $this->view->invoice = $invoice;
    }

    public function searchAction()
    {
        if (is_null(($term = $this->_getParam('search')))) {
            $this->_helper->redirector('list');
        }
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoices = $invoiceMapper->searchByInvoiceNumber($term);
        $sellerMapper = new Application_Model_SellerMapper();
        $sellers = array();
        foreach ($invoices as $invoice) {
            if (!isset($sellers[$invoice->getSellerId()])) {
                $sellers[$invoice->getSellerId()] = $sellerMapper->find($invoice->getSellerId());
            }
        }
        $this->view->term = $term;
        $this->view->sellers = $sellers;
        $this->view->invoices = $invoices;
    }

    public function downloadAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }
        $this->_helper->layout()->disableLayout();
        $this->getResponse()->setHeader('Content-type', 'text/csv');
        $this->getResponse()->setHeader('Content-disposition', 'attachment;filename=invoice-' . $invoice->getSellerId() . '-' . $invoice->getSellerInvoiceNumber() . '.csv');
        $this->view->items = $invoice->getLineItems();
    }

    public function updateAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }
        if ($this->getRequest()->isPost() && !is_null($newInvoiceNumber = $this->_getParam('invoice-number'))) {
            try {
                $oldInvoiceNumber = $invoice->getSellerInvoiceNumber();
                $newInvoiceNumber = trim($newInvoiceNumber);
                if(empty($newInvoiceNumber)) {
                    throw new Exception('Empty invoice number');
                }
                $invoice->setSellerInvoiceNumber($newInvoiceNumber);
                $action = new Application_Model_InvoiceAction();
                $action->setActionDate(Zend_Date::now());
                $action->setInvoiceId($invoice->getId());
                $action->setWhat($action::WHAT_INVOICE_NUMBER_UPDATED);
                $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
                $invoice->addAction($action);
                $invoiceMapper->save($invoice);
                $view = new Zend_View();
                $view->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $view->invoiceId = $invoice->getId();
                $sellerMapper = new Application_Model_SellerMapper();
                $seller = $sellerMapper->find($invoice->getSellerId());
                $view->companyName = $seller->getName();
                $view->oldInvoiceNumber = $oldInvoiceNumber;
                $view->newInvoiceNumber = $newInvoiceNumber;

                $mail = new Zend_Mail();
                $mail->addTo($this->getInvokeArg('bootstrap')->getOption('updateNotifications'));
                $subject = 'Updated invoice number on Sell Books Direct';
                $mail->setSubject($subject);
                $mail->setBodyText($view->render('updated-invoice.phtml'));
                $mail->send();

                $this->_helper->flashMessenger->setNamespace('success');
                $this->_helper->flashMessenger('Invoice number successfully updated to: '.$newInvoiceNumber);
            } catch(Exception $e) {
                $this->_helper->flashMessenger->setNamespace('alert');
                $msg = 'Invoice number failed to update. ';
                if(stripos($e->getMessage(), 'duplicate entry') !== false) {
                    $msg .= 'The invoice number is a duplicate to what has been already used.';
                }
                $this->_helper->flashMessenger($msg);
            }
        }
        $this->_helper->redirector('view', null, null, array('id'=>$id));
    }

}
