<?php

class Api_InvoicesTodayController extends Zend_Rest_Controller
{

    public function indexAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        $json = array('error'=>'403','message'=>'Listing Not Allowed');
        $this->_helper->json($json);
    }

    public function getAction()
    {
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoices = $invoiceMapper->fetchAllCreatedAfter5PmYesterday();
        if (empty($invoices)) {
            $this->getResponse()->setHttpResponseCode(404);
            $json = array('error'=>'404','message'=>'No invoices created today');
            $this->_helper->json($json);
        }
        $json = array();
        foreach ($invoices as $invoice) {
            /* @var $invoice Application_Model_Invoice */
            $json[] = array(
                'id' => $invoice->getId(),
                'sellerId' => $invoice->getSellerId(),
                'sellerInvoiceNumber' => $invoice->getSellerInvoiceNumber(),
                'shipDate' => $invoice->getShipDate()->getIso(),
                'createTs' => $invoice->getCreateTs()->getIso(),
                'invoiceStatus' => $invoice->getInvoiceStatus(),
                'buyListName' => $invoice->getBuyListName(),
            );
        }
        $this->_helper->json($json);
    }

    public function postAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $json = array('error'=>'405','message'=>'Insertion Not Allowed');
        $this->_helper->json($json);
    }

    public function putAction()
    {

    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $json = array('error'=>'405','message'=>'Deletion Not Allowed');
        $this->_helper->json($json);
    }

}
