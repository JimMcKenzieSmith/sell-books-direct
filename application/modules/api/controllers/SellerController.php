<?php

class Api_SellerController extends Zend_Rest_Controller
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
        $sellerMapper = new Application_Model_SellerMapper();
        $seller = $sellerMapper->find($id);
        if (is_null($seller)) {
            $this->getResponse()->setHttpResponseCode(404);
            $json = array('error'=>'404','message'=>'Seller does not exist');
            $this->_helper->json($json);
        }
        $json = array(
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
                'sellerStatus' => $seller->getSellerStatus()
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
        $sellerMapper = new Application_Model_SellerMapper();
        $seller = $sellerMapper->find($id);
        if (is_null($id)) {
            $this->getResponse()->setHttpResponseCode(404);
            $json = array('error'=>'404','message'=>'Seller does not exist');
            $this->_helper->json($json);
        }
        try {
            $data = Zend_Json::decode($this->getRequest()->getRawBody());
        } catch (Zend_Json_Exception $e) {
            $this->getResponse()->setHttpResponseCode(400);
            $json = array('error'=>'400','message'=>'JSON error. ' . $e->getMessage());
            $this->_helper->json($json);
        }
        if (is_null($data)) {
            $this->getResponse()->setHttpResponseCode(400);
            $json = array('error'=>'400','message'=>'No JSON received');
            $this->_helper->json($json);
        }
        $seller->exchangeArray($data);
        $sellerMapper->save($seller);
        $json = array('error'=>'200','message'=>'Seller updated');
        $this->_helper->json($json);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $json = array('error'=>'405','message'=>'Deletion Not Allowed');
        $this->_helper->json($json);
    }

}
