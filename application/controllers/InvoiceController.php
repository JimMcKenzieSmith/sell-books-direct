<?php

/**
 * Invoice Controller
 *
 * @package SellBooksDirect
 * @subpackage Controller
 * @version $Id: f1cdf8e289953ef12b2fe07d65dc785ee9dcf4ed $
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
class InvoiceController extends Zend_Controller_Action
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
        $invoices = $invoiceMapper->fetchAllBySeller($this->view->user->getId());
        $sortedInvoices = array(
                Application_Model_Invoice::STATUS_PENDING_APPROVAL => array(),
                Application_Model_Invoice::STATUS_APPROVED => array(),
                Application_Model_Invoice::STATUS_RECEIVED => array(),
                Application_Model_Invoice::STATUS_PROCESSED => array(),
                Application_Model_Invoice::STATUS_PAID => array(),
                Application_Model_Invoice::STATUS_CANCELLED => array(),
                );
        foreach ($invoices as $invoice) {
            /* @var $invoice Application_Model_Invoice */
            $sortedInvoices[$invoice->getInvoiceStatus()][] = $invoice;
        }
        $this->view->downloadLink = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
        $this->view->downloadLinkForNewSellers = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
        $this->view->invoices = $sortedInvoices;
    }

    public function viewAction()
    {

        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }

        $this->view->downloadLink = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
        $this->view->downloadLinkForNewSellers = $this->getInvokeArg('bootstrap')->getOption('shippingInstructions');
        $this->view->invoice = $invoice;
    }

    public function downloadAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }

        $this->_helper->layout()->disableLayout();

        $csv = "ISBN13,Price,Quantity\n";
        foreach ($invoice->getLineItems() as $item) {
            $data = array('="' . $item->getIsbn13() . '"', $item->getPrice(), $item->getQuantity());
            $csv .= implode(',', $data) . "\n";
        }

        $this->view->csv = $csv;
        $this->view->filename = 'invoice-' . $invoice->getSellerInvoiceNumber() . '.csv';
    }

    public function cancelAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        if (is_null($id = $this->_getParam('id')) || is_null($invoice = $invoiceMapper->find($id))) {
            $this->_helper->redirector('list');
        }
        if ($invoice->getInvoiceStatus() == $invoice::STATUS_PENDING_APPROVAL || $invoice->getInvoiceStatus() == $invoice::STATUS_APPROVED) {
            $invoice->setInvoiceStatus($invoice::STATUS_CANCELLED);
            $action = new Application_Model_InvoiceAction();
            $action->setActionDate(Zend_Date::now());
            $action->setInvoiceId($invoice->getId());
            $action->setWhat($action::WHAT_CANCELLED);
            $action->setWho($this->view->user->getContactName() . ' (' . $this->view->user->getName() . ')');
            $invoice->addAction($action);
            $invoiceMapper->save($invoice);
        }
        $this->_helper->redirector('view', null, null, array('id'=>$id));
    }



}
