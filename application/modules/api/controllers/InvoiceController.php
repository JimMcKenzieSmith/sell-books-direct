<?php

class Api_InvoiceController extends Zend_Rest_Controller
{

    public function indexAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        $json = array('error'=>'403','message'=>'Listing Not Allowed');
        $this->_helper->json($json);
    }

    public function getAction()
    {
        $id = $this->_getParam('id');
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoice = $invoiceMapper->find($id);
        if (is_null($invoice)) {
            $this->getResponse()->setHttpResponseCode(404);
            $json = array('error'=>'404','message'=>'Invoice does not exist');
            $this->_helper->json($json);
        }
        $items = array();
        foreach ($invoice->getLineItems() as $item) {
            /* @var $item Application_Model_InvoiceItem */
            $items[] = array(
                    'isbn13' => $item->getIsbn13(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getPrice(),
                    );
        }
        $json = array(
                'id' => $invoice->getId(),
                'sellerId' => $invoice->getSellerId(),
                'sellerInvoiceNumber' => $invoice->getSellerInvoiceNumber(),
                'shipDate' => $invoice->getShipDate()->getIso(),
                'createTs' => $invoice->getCreateTs()->getIso(),
                'invoiceStatus' => $invoice->getInvoiceStatus(),
                'buyListName' => $invoice->getBuyListName(),
                'lineItems' => $items,
                );
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
        $id = $this->_getParam('id');
        $invoiceMapper = new Application_Model_InvoiceMapper();
        $invoice = $invoiceMapper->find($id);
        if (is_null($invoice)) {
            $this->getResponse()->setHttpResponseCode(404);
            $json = array('error'=>'404','message'=>'Invoice does not exist');
            $this->_helper->json($json);
        }
        $data = Zend_Json::decode($this->getRequest()->getRawBody());
        $invoice->exchangeArray($data);
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
            $action->setWho('SPARK');
            $invoice->addAction($action);
        }
        $invoiceMapper->save($invoice);
        $json = array('error'=>'200','message'=>'Invoice updated');
        $this->_helper->json($json);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $json = array('error'=>'405','message'=>'Deletion Not Allowed');
        $this->_helper->json($json);
    }

}
